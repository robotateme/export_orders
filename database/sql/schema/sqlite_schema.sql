DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS merchandise;
DROP TABLE IF EXISTS clients;

CREATE TABLE clients (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL
);

CREATE TABLE merchandise (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    price INTEGER NOT NULL CHECK (price >= 0)
);

CREATE TABLE orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    item_id INTEGER NOT NULL,
    customer_id INTEGER NOT NULL,
    comment TEXT NOT NULL,
    status TEXT NOT NULL CHECK (status IN ('new', 'complete')),
    order_date DATE NOT NULL,
    price INTEGER NOT NULL CHECK (price >= 0),
    FOREIGN KEY (item_id) REFERENCES merchandise (id),
    FOREIGN KEY (customer_id) REFERENCES clients (id)
);

INSERT INTO clients (id, name) VALUES
    (1, 'Alice'),
    (2, 'Bob'),
    (3, 'Carol');

INSERT INTO merchandise (id, name, price) VALUES
    (1, 'Keyboard', 10000),
    (2, 'Mouse', 5000),
    (3, 'Monitor', 30000);
