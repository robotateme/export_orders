/*
  Индексы для PostgreSQL.

  clients.id и merchandise.id являются primary key, отдельные индексы на них не нужны.
*/

-- Ускоряет 2a: поиск заказов клиента за последние 7 дней.
-- Также может использоваться в 2b, так как customer_id является первым полем индекса.
CREATE INDEX idx_orders_customer_date ON orders (customer_id, order_date);

-- Ускоряет 2c: агрегацию суммы заказов по клиенту.
CREATE INDEX idx_orders_customer_price ON orders (customer_id, price);

-- Ускоряет 2d: проверку наличия complete-заказов по товару.
-- Частичный индекс компактнее общего (item_id, status), так как хранит только complete-заказы.
CREATE INDEX idx_orders_complete_item
    ON orders (item_id)
    WHERE status = 'complete';
