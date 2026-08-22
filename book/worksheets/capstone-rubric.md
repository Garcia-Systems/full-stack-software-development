# Capstone evaluation rubric

Score each dimension **0–3**: 0 absent/unsafe, 1 partial, 2 sound with evidence, 3 unusually clear and adversarially proven. A passing submission scores at least 26/39, has no zero in security, authorization/isolation, concurrency, or deployment, and completes the representative E2E journey. Line count and resemblance to an instructor design are irrelevant.

| Dimension | Evidence for a sound (2) result |
| --- | --- |
| Requirement reasoning | Ambiguities, assumptions, invariants, and acceptance outcomes are explicit. |
| Architecture | Boundaries and owners are justified in an accurate diagram. |
| Data modeling | Constraints, indexes, lifecycle, token hash, and migration are reasoned about. |
| API design | Contracts and important status/error behavior are defined before implementation. |
| Frontend behavior | Loading, empty, success, validation, authorization, and failure states are usable. |
| Authentication/authorization | Identity, role rules, and server-side permission checks are proven. |
| Security/isolation | Cross-tenant access, token secrecy/expiry/replay, escalation, and safe logging are tested. |
| Concurrency | A deterministic race proves only one acceptance can win. |
| Reliability | Delivery failure is decoupled, durable, retriable, and diagnosable. |
| Testing | Risk-based unit/database/API/React/E2E evidence protects behavior. |
| Observability | Correlation, audit events, version, and operational investigation are useful and secret-free. |
| Deployment | CI, build, migration, startup, verification, worker, and rollback reasoning are demonstrated. |
| Communication | The report is concise, traceable, and candid about tradeoffs. |

## Reviewer adversarial checks

- Attempt creation as viewer and across organizations.
- Use an invalid, expired, revoked, already accepted, and wrong-email token.
- release two acceptance transactions at the same barrier and inspect membership/audit counts.
- retry invitation creation and acceptance at the HTTP boundary.
- stop the worker, create an invitation, then recover delivery without losing the invitation.
- search logs, queue payloads, database rows, and audit metadata for raw tokens.
- change a role across tenant boundaries and attempt to grant a role the actor cannot grant.
- deploy onto the previous schema and confirm the rehearsal detects ordering rather than serving traffic.
