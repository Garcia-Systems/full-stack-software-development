# 10. Routes and Controllers

[Previous](09-real-laravel-application.md) · [Next](11-models-and-domain-objects.md)

Routing is dispatch: **method + path → handler**. It does not validate ticket content or run SQL. RelayDesk declares list, create, show, update, delete, and status routes in `app/routes/api.php`; `TicketController` owns HTTP orchestration.

```sh
docker compose exec web php artisan route:list --path=api/tickets
curl -i http://localhost:8080/api/tickets/1
curl -i -X POST http://localhost:8080/api/tickets/1
curl -i http://localhost:8080/api/ticket/1
curl -i http://localhost:8080/api/tickets/not-a-number
```

The first request reaches `show`. The next is `405` because the path exists but not for POST. The singular typo and nonnumeric parameter are `404` routing failures. A numeric missing ticket is also `404`, but only after route matching and implicit model binding queries persistence. Use `route:list`, method/path, and the response—not intuition—to distinguish them.

## Move work to its owner

A route declaration should identify dispatch. The controller translates `Request`/validated input into an operation and response. It should not become a second router or a home for every business rule. Dependency injection in `transition` asks the container for `TicketWorkflow`; the controller does not manually assemble it.

**Controlled failure:** change neither code nor data; deliberately request `/api/tickets/nope`. Record status and request ID, compare `route:list`, then retry `/api/tickets/999`. Similar client symptoms have different investigations: the former fails the numeric route constraint, while the latter reaches model binding. Run `scripts/lab chapter 10 verify` after restoring the correct request.
