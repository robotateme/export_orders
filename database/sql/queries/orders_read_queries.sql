/*
  SQL-запросы для структуры:
    clients (id, name)
    merchandise (id, name, price)
    orders (id, item_id, customer_id, comment, status, order_date, price)

  price хранится в минимальных денежных единицах.
  Синтаксис ниже рассчитан на PostgreSQL. Для MySQL выражение
  CURRENT_DATE - INTERVAL '7 days' нужно заменить на DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY).
*/

-- 2a. Имена всех клиентов, которые не делали заказы в последние 7 дней.
SELECT c.name
FROM clients AS c
WHERE NOT EXISTS (
    SELECT 1
    FROM orders AS o
    WHERE o.customer_id = c.id
      AND o.order_date >= CURRENT_DATE - INTERVAL '7 days'
);

-- 2b. Имена 5 клиентов, которые сделали больше всего заказов.
SELECT c.name
FROM clients AS c
JOIN orders AS o ON o.customer_id = c.id
GROUP BY c.id, c.name
ORDER BY COUNT(*) DESC, c.id
LIMIT 5;

-- 2c. Имена 10 клиентов, которые сделали заказы на наибольшую сумму.
SELECT c.name
FROM clients AS c
JOIN orders AS o ON o.customer_id = c.id
GROUP BY c.id, c.name
ORDER BY SUM(o.price) DESC, c.id
LIMIT 10;

-- 2d. Имена всех товаров, по которым не было доставленных заказов со статусом complete.
SELECT m.name
FROM merchandise AS m
WHERE NOT EXISTS (
    SELECT 1
    FROM orders AS o
    WHERE o.item_id = m.id
      AND o.status = 'complete'
);
