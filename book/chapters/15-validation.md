# 15. Validation: Receiving Is Not Accepting

[Previous](14-relationships.md) · [Next](16-services-and-business-logic.md)

```mermaid
flowchart LR
 B[Browser hints] --> H[HTTP input] --> V[Form Request validation] --> R[Business rules] --> D[Database constraints]
```

Each boundary answers a different question. Browser `required` improves interaction but is bypassable. `StoreTicketRequest` rejects malformed shapes and cross-organization IDs. Services reject state-dependent business operations. MySQL constraints remain when every application is bypassed.

```sh
# missing
curl -i -H 'Content-Type: application/json' -d '{}' http://localhost:8080/api/tickets
# malformed value
curl -i -H 'Content-Type: application/json' -d '{"organization_id":1,"customer_id":"abc","subject":"x"}' http://localhost:8080/api/tickets
# unacceptable relationship
curl -i -H 'Content-Type: application/json' -d '{"organization_id":1,"customer_id":3,"subject":"cross tenant"}' http://localhost:8080/api/tickets
# valid
curl -i -H 'Content-Type: application/json' -d '{"organization_id":1,"customer_id":1,"subject":"accepted","priority":"high"}' http://localhost:8080/api/tickets
```

The first three produce `422` with a field-keyed `errors` object and request ID header; no INSERT occurs. Malformed JSON itself produces Laravel's request error response before these semantic rules. Inspect response plus row count.

Validation is not a synonym for all correctness. “Customer must be active before a project is opened” depends on existing state and belongs to the operation in Chapter 16. Run `vendor/bin/phpunit --filter=validation` to preserve the boundary contract.
