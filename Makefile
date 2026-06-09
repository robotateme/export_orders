PHP ?= php8.4
COMPOSER ?= $(shell command -v composer)
SQLITE_DB ?= tests/shop.sqlite
IMPORT_INPUT ?= data/orders_input.txt
IMPORT_INVALID ?= data/invalid_orders.txt
IMPORT_DATE ?= 2026-06-09

.DEFAULT_GOAL := help

.PHONY: help install test analyse check sqlite-setup import-sqlite smoke clean

help:
	@printf '%s\n' 'Targets:'
	@printf '%s\n' '  make install       Install Composer dependencies'
	@printf '%s\n' '  make test          Run PHPUnit'
	@printf '%s\n' '  make analyse       Run PHPStan level 8'
	@printf '%s\n' '  make check         Run tests and static analysis'
	@printf '%s\n' '  make sqlite-setup  Recreate local SQLite database'
	@printf '%s\n' '  make import-sqlite Import test data into local SQLite database'
	@printf '%s\n' '  make smoke         Recreate SQLite DB, import data, compare invalid rows'
	@printf '%s\n' '  make clean         Remove generated local runtime files'

install:
	$(PHP) $(COMPOSER) install

test:
	$(PHP) vendor/bin/phpunit

analyse:
	$(PHP) vendor/bin/phpstan analyse

check: test analyse

sqlite-setup:
	$(PHP) -r '$$db = new PDO("sqlite:" . __DIR__ . "/$(SQLITE_DB)"); $$db->exec(file_get_contents(__DIR__ . "/database/sql/schema/sqlite_schema.sql"));'

import-sqlite:
	DB_DSN="sqlite:$(CURDIR)/$(SQLITE_DB)" \
	IMPORT_INPUT_PATH="$(IMPORT_INPUT)" \
	IMPORT_INVALID_PATH="$(IMPORT_INVALID)" \
	IMPORT_ORDER_DATE="$(IMPORT_DATE)" \
	$(PHP) import_orders.php

smoke: sqlite-setup import-sqlite
	diff -u data/expected_invalid_orders.txt $(IMPORT_INVALID)

clean:
	rm -f .phpunit.result.cache data/invalid_orders.txt tests/*.sqlite
	rm -rf var
