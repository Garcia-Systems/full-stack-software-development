# 11. Models and Domain Objects

[Previous](10-routes-and-controllers.md) · [Next](12-migrations-and-schema-design.md)

A database row is stored values. An Eloquent model is a PHP object that maps attributes and relationships to a row. “Customer is active” is a business concept whose consequences need not live in that model. RelayDesk uses `Organization`, `Customer`, `Project`, and `Ticket` models; Chapter 16 will put meaningful operations in services.

## Compare representations

```sh
docker compose exec db mysql -urelaydesk -prelaydesk relaydesk -e 'SELECT id,subject,status,version,created_at FROM tickets WHERE id=1\G'
docker compose exec web php artisan tinker --execute='dump(App\Models\Ticket::with("customer")->find(1)->toArray());'
```

MySQL returns typed columns using its wire representation. Eloquent supplies an object, casts `version` to an integer, provides methods, tracks dirty attributes, and can load `customer`. JSON serialization is yet another representation. None is the domain itself.

## Controlled mass-assignment assumption

Try in Tinker:

```php
$ticket = App\Models\Ticket::findOrFail(1);
$ticket->fill(['subject' => 'Inspected', 'version' => 99]);
[$ticket->getDirty(), $ticket->version];
```

`version` is not fillable: receiving an array does not authorize every field. Inspect `Ticket::$fillable`. Explicitly managed versioning prevents clients from overwriting concurrency state. Save only if you intend to mutate, or run `make reset`. Evidence is the dirty-attribute array before SQL. This is a transport-safety mechanism, not a complete business-rule system.
