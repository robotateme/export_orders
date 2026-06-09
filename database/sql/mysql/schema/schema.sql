DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS merchandise;
DROP TABLE IF EXISTS clients;

CREATE TABLE clients (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE merchandise (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CHECK (price >= 0)
);

CREATE TABLE orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    item_id BIGINT UNSIGNED NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    comment TEXT NOT NULL,
    status ENUM('new', 'complete') NOT NULL,
    order_date DATE NOT NULL,
    price BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_orders_item FOREIGN KEY (item_id) REFERENCES merchandise (id),
    CONSTRAINT fk_orders_customer FOREIGN KEY (customer_id) REFERENCES clients (id),
    CHECK (price >= 0)
);
