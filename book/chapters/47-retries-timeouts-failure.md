# Chapter 47: Retries, Timeouts, and Failure

> Part VI — Build Systems That Survive Reality

## An explicit failure policy

A dependency call without a deadline can consume a worker indefinitely. RelayDesk sets a 300 ms connection timeout and a 1 second whole-request timeout. The HTTP client permits two additional attempts only for connection failures, 429, and 5xx. It does not retry 4xx validation/rejection or malformed successful data. The queue gives the job at most three executions; both layers are bounded, though layering retries can multiply calls and must be budgeted.

```mermaid
flowchart LR
 A1[Attempt 1] --> F1[503]
 F1 --> W1[bounded wait]
 W1 --> A2[Attempt 2]
 A2 --> F2[503]
 F2 --> W2[bounded wait]
 W2 --> A3[Attempt 3]
 A3 --> S[success]
```

`transient` resets its counter and fails exactly twice before succeeding. `persistent` never recovers, so retries stop and the delivery becomes `failed`. Retry log events include correlation, dependency, attempt, status, and whether policy considered it retryable. Backoff is deterministic in this lab; production fleets often add jitter so clients do not synchronize.

## Lab

Set transient mode, enqueue one ticket, run the worker, then correlate the simulator attempt with `dependency.retry`. Repeat persistent mode and prove the queue has stopped rather than claiming it is “still trying.” Use delay mode to prove the application timeout occurs before the simulator response. Use client-error to prove it is not retried.

Retries consume dependency capacity and may amplify an incident. They are safe here only because the fake provider's educational acceptance is keyed by ticket and Chapter 48 protects RelayDesk's own business operation. A real provider also needs a documented idempotency contract.

References: [Laravel HTTP retries and timeouts](https://laravel.com/docs/12.x/http-client#timeout), [AWS Builders' Library on timeouts/backoff/jitter](https://aws.amazon.com/builders-library/timeouts-retries-and-backoff-with-jitter/).

---

[← Chapter 46](./46-external-apis.md) · [Chapter 48 →](./48-idempotency.md)
