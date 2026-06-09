DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS merchandise;
DROP TABLE IF EXISTS clients;

CREATE TABLE clients (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE merchandise (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price BIGINT NOT NULL CHECK (price >= 0)
);

CREATE TABLE orders (
    id BIGSERIAL PRIMARY KEY,
    item_id BIGINT NOT NULL REFERENCES merchandise (id),
    customer_id BIGINT NOT NULL REFERENCES clients (id),
    comment TEXT NOT NULL,
    status VARCHAR(16) NOT NULL CHECK (status IN ('new', 'complete')),
    order_date DATE NOT NULL,
    price BIGINT NOT NULL CHECK (price >= 0)
);
