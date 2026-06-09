/*
  Индексы для запросов из database/sql/queries/orders_read_queries.sql.

  clients.id и merchandise.id предполагаются первичными ключами, поэтому
  отдельные индексы на эти поля создавать не нужно.
*/

-- Ускоряет 2a: поиск заказов клиента за последние 7 дней.
-- Также может использоваться в 2b, так как customer_id является первым полем индекса.
CREATE INDEX idx_orders_customer_date ON orders (customer_id, order_date);

-- Ускоряет 2c: агрегацию суммы заказов по клиенту.
CREATE INDEX idx_orders_customer_price ON orders (customer_id, price);

-- Ускоряет 2d: проверку наличия complete-заказов по товару.
CREATE INDEX idx_orders_item_status ON orders (item_id, status);

/*
  Если СУБД поддерживает частичные индексы (например PostgreSQL), для 2d
  эффективнее и компактнее:

  CREATE INDEX idx_orders_complete_item
      ON orders (item_id)
      WHERE status = 'complete';

  В этом случае общий idx_orders_item_status может быть не нужен.
*/
