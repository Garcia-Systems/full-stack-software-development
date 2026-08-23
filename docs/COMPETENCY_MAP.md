# RelayDesk competency map

“Introduced” supplies the first usable model, “practiced” requires later executable evidence, and “independent” removes most scaffolding. Empty cells would indicate an educational gap; the final column intentionally converges on the Chapter 60 invitation brief.

| Capability | Introduced | Practiced | Demonstrated independently |
| --- | --- | --- | --- |
| Locate a browser/HTTP/server/database boundary | Ch. 1–2 | Ch. 4–8, 17 | Ch. 58, 60 |
| Reconstruct a request with correlation evidence | Ch. 2 | Ch. 38, 50, 58 | Ch. 60 |
| Design route → validation → service → persistence → response | Ch. 9–16 | Ch. 17, 35–38 | Ch. 60 |
| Explain ORM operations as SQL and persistent state | Ch. 18 | Ch. 19, 24, 54 | Ch. 60 |
| Diagnose indexes and query plans | Ch. 20–21 | Ch. 24, 51 | Ch. 60 (when workload justifies it) |
| Protect atomicity and integrity | Ch. 22, 24 | Ch. 49, 54 | Ch. 60 |
| Reason about concurrent updates | Ch. 23 | Ch. 49, 58 | Ch. 60 invitation acceptance |
| Model event → state → render | Ch. 25, 28–30 | Ch. 32–34, 56 | Ch. 60 |
| Model async completion/failure as state | Ch. 26, 31 | Ch. 38–39, 56–57 | Ch. 60 |
| Define and inspect a stable API contract | Ch. 35–37 | Ch. 38–39, 55 | Ch. 60 |
| Authenticate and distinguish identity from authority | Ch. 40, 42 | Ch. 43, 55, 57–58 | Ch. 60 |
| Enforce authorization and tenant isolation | Ch. 41 | Ch. 55, 57–58 | Ch. 60 |
| Explain CORS, cookies, sessions, and CSRF boundaries | Ch. 42–43 | Ch. 55, 59 | Ch. 60 |
| Diagnose cache ownership and invalidation | Ch. 44 | Ch. 50–51, 58 | Ch. 60 where caching is justified |
| Observe queued work and worker failure | Ch. 45 | Ch. 46–47, 50, 58–59 | Ch. 60 delivery |
| Classify dependency failures and bound retries/timeouts | Ch. 46–47 | Ch. 50, 58–59 | Ch. 60 delivery |
| Make duplicate operations idempotent | Ch. 48 | Ch. 55, 58 | Ch. 60 |
| Reason by value, ownership, timing, ordering, and count | Ch. 49–50 | Ch. 51, 58 | Ch. 60 |
| Choose a test level for the disputed boundary | Ch. 52 | Ch. 53–58 | Ch. 60 evidence plan |
| Build, configure, migrate, verify, recover, and roll back | Ch. 59 | Ch. 59 drills | Ch. 60 release report |
| Explain engineering decisions to business and technical readers | Ch. 1 onward | Ch. 58 evidence record | Ch. 60 engineering report |

## Progression finding

The map has no major capability that is introduced and then abandoned. Chapter 60 appropriately requires transfer rather than providing another recipe. Cache use is conditional in the capstone because adding a cache without demonstrated pressure would contradict Chapter 44's tradeoff lesson.
