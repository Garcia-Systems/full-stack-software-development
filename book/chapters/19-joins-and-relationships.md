# 19. Joins and Relationships

[Previous](18-sql-behind-eloquent.md) · [Next](20-indexes.md)

The business asks: **which customers in organization 1 have open work, and how much?** Start with keys, not model method names. A customer row matches a ticket only when both `customer_id = customers.id` and the tenant identifiers match.

```sh
docker compose exec db mysql -urelaydesk -prelaydesk relaydesk -e '
SELECT c.id,c.name,t.id AS ticket_id,t.status
FROM customers c
LEFT JOIN tickets t ON t.customer_id=c.id AND t.organization_id=c.organization_id
WHERE c.organization_id=1
ORDER BY c.id,t.id;'
```

`INNER JOIN` would omit the dormant customer with no tickets. `LEFT JOIN` retains it and supplies `NULL` ticket columns. That result shape repeats customer columns for every matching ticket; it is not a nested Eloquent object graph. Inspect the actual keys with `SHOW CREATE TABLE customers\G` and `SHOW CREATE TABLE tickets\G`.

## Aggregate the question

```sql
SELECT c.id, c.name, COUNT(t.id) AS open_tickets
FROM customers AS c
LEFT JOIN tickets AS t
  ON t.customer_id = c.id
 AND t.organization_id = c.organization_id
 AND t.status <> 'closed'
WHERE c.organization_id = 1
GROUP BY c.id, c.name
ORDER BY c.id;
```

Putting the status condition in `ON` preserves customers with zero open work. Putting it in `WHERE` rejects their `NULL` joined row and accidentally changes the business answer.

## Observe and repair N+1

```sh
docker compose exec web php artisan lab:database relationships
docker compose exec web vendor/bin/phpunit --filter=RelationshipQueryTest
```

Lazy access performs `1 + N` queries. `Customer::with('tickets')->get()` performs two queries: one for customers and one `WHERE IN (...)` for all matching tickets. Eager loading is not an SQL join and does not always need to be replaced by one. Use eager loading when PHP needs related objects; use an aggregate join when the database should return a report shape. [Laravel documents both lazy and eager loading](https://laravel.com/docs/12.x/eloquent-relationships#eager-loading).
