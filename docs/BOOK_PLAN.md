# Full-Stack Software Engineering: Build, Break, Debug, Deploy

## 1. Status and planning assumptions

This document is the implementation blueprint, not chapter prose. The repository was inspected at commit `9dcf8ac` and currently contains only a root `.gitkeep`; there is no application, dependency manifest, automation, prose convention, CI configuration, or `AGENTS.md` to preserve. Starting from an empty repository is useful here: the project can adopt a reader-first structure without migrating a prototype. The initial commit and simple root should be preserved, while every tool and directory added later must earn its place in a chapter or in repository operation.

Decisions in this plan deliberately defer framework version pinning until the scaffold phase. At that point, use one supported Laravel/PHP/MySQL/Node combination, commit lockfiles, publish tested host requirements, and make Docker the reference environment. The book should teach durable concepts while its commands remain reproducible.

## 2. Mission, reader, and outcome

### Mission

Teach engineering judgment by evolving and diagnosing one modest SaaS product across this path:

`browser -> React/TypeScript -> HTTP/JSON -> Laravel/PHP -> MySQL -> jobs/cache/external service -> production`

The book is successful when readers can locate the boundary that owns a symptom and prove their diagnosis with evidence. Definitions are introduced only when they enable an observation, implementation, experiment, test, or operational decision.

### Target reader and prerequisites

The primary reader is a junior developer, career changer, frontend/backend specialist broadening across the stack, or experienced developer new to this stack. Readers should be able to:

- navigate files and run commands in a terminal;
- read basic programming constructs (variables, conditions, loops, and functions);
- edit text in an IDE and use a web browser;
- understand that Git records changes, although a short command reference can be supplied.

No prior Laravel, React, TypeScript, SQL, Docker, or production-operations experience is assumed. This is not an introduction to programming syntax: short prerequisite links and an environment diagnostic should redirect readers who need that foundation. Examples should run on Linux, macOS, and Windows through Docker Desktop/WSL 2, with host-native execution documented as optional.

### Final capability

By the end, a reader can turn an ambiguous business request into acceptance criteria, schema constraints, API contracts, authorized backend behavior, accessible frontend behavior, tests, deployment changes, and observable success measures. They can trace requests with browser tools and correlation IDs; inspect SQL and query plans; reason about transactions, caches, queues, retry safety, and concurrency; and use evidence to distinguish UI, network, API, domain, persistence, dependency, and infrastructure failures.

## 3. The evolving application: RelayDesk

**RelayDesk** is a multi-tenant customer-support and project-work SaaS for small service teams. An organization records customers and their projects, receives and assigns support tickets, tracks status and priority, and sees a small operational dashboard. This provides believable requirements without adding billing, chat, file storage, or a microservice fleet.

The first vertical slice is intentionally tiny: a public page lists seeded tickets and creates one. Scope grows only when a chapter needs a business reason.

### Entities and relationships

| Entity | Purpose and important relationships |
| --- | --- |
| `users` | People who authenticate; belong to many organizations through `memberships`. |
| `organizations` | Tenant/security boundary; owns all business data. |
| `memberships` | User-to-organization join with role (`owner`, `admin`, `agent`, `viewer`) and status; unique per user/organization. |
| `invitations` | Time-limited, single-use invitation token, target email, intended role, inviter, acceptance metadata. Introduced fully in the capstone. |
| `customers` | Customer organizations/contacts belonging to one RelayDesk organization. |
| `projects` | Work engagements belonging to a customer and organization. |
| `tickets` | Core work item; belongs to organization, customer, optionally project, reporter and assignee; has status, priority, subject, description, and optimistic version. |
| `ticket_comments` | Discussion/activity on a ticket, authored by a user. |
| `audit_events` | Append-only actor/action/subject metadata and safe before/after changes; supports permission and operational accountability. |
| `notifications` | In-app/delivery state associated with a user and event; email delivery happens asynchronously. |
| `integration_deliveries` | Records outbound webhook request, attempt, response, idempotency key, and status for controlled external integration. |

All tenant-owned tables carry `organization_id`, even where it is derivable, to make authorization and indexing visible; schema constraints prevent inconsistent parentage where practical. Factories/seeders generate deterministic IDs/timestamps where labs compare output. Soft deletion is avoided initially and added only if a later chapter can cover its query and integrity costs. Polymorphic abstractions, customizable workflow engines, billing, attachments, and real-time chat are explicitly out of scope.

### Key invariants

- A user sees or changes records only through an active membership in the current organization.
- A ticket's customer, project, and assignee must be valid for that organization; projects must belong to the selected customer.
- Status transitions and role changes occur through explicit domain services, not arbitrary mass assignment.
- An invitation is accepted once, by the intended identity, before expiration.
- Retried external requests/jobs do not duplicate business effects.
- Audit events are append-only and exclude secrets, tokens, and unnecessary personal data.

## 4. Architecture evolution

1. **Orientation (Chapters 1-8):** a deliberately transparent PHP endpoint, tiny browser script, and MySQL table expose process, connection, HTTP, and persistence boundaries. This disposable learning slice is replaced, not maintained in parallel.
2. **Backend monolith (9-24):** RelayDesk becomes one Laravel application with controllers, request validation, thin domain/application services, Eloquent, migrations, MySQL, and server-rendered/API inspection tools. Tenant scope is seeded but authentication waits until the relevant chapters.
3. **Frontend client (25-34):** a Vite React/TypeScript SPA begins against deterministic fixtures and a mock adapter. Feature-oriented folders (`tickets`, `customers`, shared API/UI) keep boundaries visible without a heavy state framework.
4. **Integrated application (35-43):** a versioned `/api/v1` REST/JSON contract joins the SPA and Laravel. Laravel Sanctum's first-party cookie/session approach is the default; policies enforce organization membership. Separate dev origins make CORS/CSRF observable, while the production image serves same-origin assets.
5. **Production behaviors (44-51):** Redis is introduced only now for cache and queue. A repository-owned fake webhook service simulates latency/status/malformed payloads. Jobs, timeouts, retries, idempotency, structured logs, request IDs, and measurements are added around the modular monolith.
6. **Proof (52-58):** the existing tests are organized by risk and evidence rather than a mandatory pyramid, Playwright covers two critical journeys, and a deterministic neutral profile drives the full-stack debugging capstone.
7. **Delivery (59):** Docker Compose plus CI build one deployable web image and one worker process from the same code. A production-like Compose target, health/readiness, verified backup/restore, and rollback/incident drills are implemented without claiming a universal platform.
8. **Capstone (60):** the independent invitations, role administration, and permission audit brief integrates every boundary without changing the architecture; the repository supplies a report, rubric, adversarial evaluation strategy, and separate instructor approach rather than a default implementation.

The target is a **modular monolith**, not microservices: one Laravel backend, one React client, one MySQL database, optional Redis, and one local fake dependency. Interfaces are added at volatile boundaries (email/webhook/time), not around every class.

## 5. Proposed curriculum and concrete chapter work

The book remains exactly 60 substantial chapters. Every row names an observable artifact and a controlled break or proof. “Depends” lists the strongest prerequisites rather than every earlier chapter.

### Part I — See the Whole System

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 1 | Full-Stack Means Boundaries | Run a prebuilt request trace, annotate browser-to-row boundaries, then classify three symptoms by evidence rather than vocabulary. | Environment check |
| 2 | Follow a Request Through the Stack | Add a request ID, inspect Network response, server log, and SQL log, and reconstruct one timeline. Break a response field name. | 1 |
| 3 | The Browser Is a Runtime | Use DevTools Sources/Console/DOM, change a small script, trigger a runtime error, and distinguish DOM state from persisted state. | 1 |
| 4 | HTTP: The Contract Between Systems | Send equivalent requests with browser and `curl`; inspect method, headers, body, status, and deliberately mismatch content type. | 2-3 |
| 5 | URLs, DNS, Ports, and Connections | Map localhost names/ports with `curl -v` and container DNS; point to a closed/wrong port and identify connection versus HTTP failure. | 4 |
| 6 | The Server Is a Process | Start/stop a minimal PHP server, inspect PID/stdout/environment, create a port collision, and prove no response exists without a listener. | 5 |
| 7 | Databases Make State Persistent | Insert/query/update a ticket row, restart application processes, then break credentials and separate connection failure from query failure. | 6 |
| 8 | Build the Smallest Full Stack | Assemble form -> fetch -> PHP -> MySQL -> JSON -> DOM; diagnose and repair a seeded column-name mismatch; smoke-test the slice. | 3-7 |

### Part II — Build the Backend

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 9 | Replace the Prototype with Laravel | Scaffold/configure Laravel, compare framework request lifecycle with Chapter 8, and fix a deliberately stale config cache. | 8 |
| 10 | Routes, Controllers, and Boundaries | Add ticket list/show routes and thin controllers; inspect route table; debug route order/parameter binding. | 9 |
| 11 | Models Are Not the Whole Domain | Create Ticket/Customer models and factories, inspect attributes/casts, and expose then repair a mass-assignment assumption. | 10 |
| 12 | Migrations and Schema Evolution | Create initial tenant/customer/ticket schema, migrate/rollback/reset, and recover from an incompatible sample migration safely. | 7, 11 |
| 13 | CRUD as Observable Behavior | Implement ticket create/read/update/delete with explicit statuses; follow each SQL mutation and detect a false-success update. | 10-12 |
| 14 | Relationships and Tenant Scope | Add customer/project/ticket relations and tenant filtering; seed two organizations and demonstrate/fix cross-tenant leakage. | 12-13 |
| 15 | Validation at the Boundary | Add Form Requests and useful 422 errors; submit missing, malformed, and cross-tenant IDs; distinguish validation from domain rules. | 13-14 |
| 16 | Services and Business Rules | Move ticket assignment/status transitions into a service; enforce transition invariants; unit-test rejected transitions. | 15 |
| 17 | Failures, Exceptions, and Error Contracts | Centralize safe JSON problem responses; trigger not-found/domain/unexpected errors and verify logs retain detail while clients do not. | 10, 16 |

### Part III — Understand the Database

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 18 | Read the SQL Behind Eloquent | Capture ORM SQL/bindings and rewrite representative ticket queries by hand; cause and find an N+1 query. | 14 |
| 19 | Joins, Aggregates, and Relationships | Build customer workload/report queries with joins/grouping; compare inner/left joins against known fixture counts. | 18 |
| 20 | Indexes as Workload Decisions | Seed a deterministic large dataset, measure a filtered ticket query before/after a composite index, and note write/storage cost. | 12, 18 |
| 21 | Query Plans and Performance Evidence | Read `EXPLAIN ANALYZE`, test index order/selectivity, and reject an attractive but unused index using evidence. | 20 |
| 22 | Transactions and Atomic Business Changes | Wrap ticket assignment plus audit record atomically; inject failure between writes and verify rollback. | 16, 18 |
| 23 | Concurrency and Lost Updates | Run a barrier-controlled two-client update, observe the lost update, then compare locking and optimistic-version fixes. | 22 |
| 24 | Constraints: Integrity Below the Application | Add foreign keys, unique/check constraints, provoke invalid writes outside Laravel, and map database violations safely. | 12, 15, 22 |

### Part IV — Build the Frontend

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 25 | JavaScript and the Browser Data Model | Render fixture tickets with modules and DOM APIs; inspect values/references and fix a mutation-driven display bug. | 3 |
| 26 | Asynchronous JavaScript | Fetch delayed fixtures, trace event-loop ordering, use cancellation, and prevent a stale response from overwriting a newer one. | 4, 25 |
| 27 | TypeScript Makes Contracts Checkable | Convert fixture/client code, model ticket DTOs, run type checking, and catch a backend-shaped breaking change before runtime. | 25-26 |
| 28 | React's Render Mental Model | Build ticket list/detail views and use React DevTools to explain render/commit; fix side effects performed during render. | 27 |
| 29 | Components, Props, and Composition | Extract accessible ticket cards/status UI; deliberately pass the wrong ownership/responsibility and refactor component boundaries. | 28 |
| 30 | State, Events, and Derived Values | Add filters/selection; reproduce stale state and duplicated derived state, then establish a single source of truth. | 29 |
| 31 | Effects Synchronize External Systems | Load tickets via an injected client; reproduce dependency-loop and response-race bugs; add cleanup/cancellation. | 26, 30 |
| 32 | Forms and Validation UX | Build create/edit forms with typed state and server-error placeholders; test focus, pending, client and server validation behavior. | 27, 30 |
| 33 | Routing and Navigation | Add list/detail/edit URLs, direct-load and not-found behavior; diagnose a refresh fallback failure. | 29-32 |
| 34 | Frontend Architecture by Feature | Organize tickets/customers/shared API modules, add error boundary, and test that domain code is not coupled to fixture transport. | 27-33 |

### Part V — Connect the Stack

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 35 | Design the API from User Outcomes | Write the ticket list/create contract and examples before code; test pagination/filter decisions against UI needs and an OpenAPI sketch. | 15, 32-34 |
| 36 | JSON, Resources, and Serialization | Implement Laravel resources, timestamps/nulls/relations, and catch accidental sensitive/oversized fields with contract assertions. | 17, 35 |
| 37 | REST and HTTP Semantics in Practice | Choose methods/statuses/idempotence/cache headers; use conditional update semantics and debug a wrong `200`/`204` client assumption. | 4, 35-36 |
| 38 | React Talks to Laravel | Replace fixture adapter with a typed fetch client; trace one action end-to-end and repair a request/response contract mismatch. | 34-37 |
| 39 | Loading, Error, Empty, and Stale States | Use deterministic delay/empty/500 controls; add retry/cancel UX and prove stale content cannot masquerade as current. | 26, 31, 38 |
| 40 | Sessions, Cookies, and Identity | Add login/logout/current-user with Sanctum session cookies; inspect cookie flags and CSRF flow, then fix a missing-CSRF failure. | 37-38 |
| 41 | Authorization and Tenant Permissions | Implement policies for roles/ownership, distinguish 401/403/404, and use two-tenant fixtures to close an IDOR defect. | 14, 40 |
| 42 | Authentication Threats and Token Trade-offs | Run session fixation/expiry/token-storage thought experiments backed by requests; document why first-party cookies are chosen and harden flows. | 40-41 |
| 43 | CORS and Browser Security Boundaries | Run separate origins, inspect preflight/credentials, break one allowed origin, and fix configuration without using wildcard credentials. | 4-5, 40 |

### Part VI — Build Systems That Survive Reality

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 44 | Caching and Invalidation | Cache dashboard counts in Redis, measure hits/misses, reproduce stale counts, and implement/test invalidation and tenant-safe keys. | 19, 38 |
| 45 | Queues and Background Jobs | Queue ticket notifications, inspect lifecycle/failed jobs, run a deterministic failure, retry, and verify request latency improves. | 17, 40 |
| 46 | External APIs Behind an Adapter | Send ticket-created webhooks to the local fake service; validate/mask configuration and handle malformed/status responses. | 35, 45 |
| 47 | Timeouts, Retries, and Failure Budgets | Script delay/429/500 sequences; set bounded timeouts and exponential backoff; show retry amplification and record terminal failure. | 45-46 |
| 48 | Idempotency and At-Least-Once Delivery | Duplicate create requests and job delivery; add idempotency keys/unique effects and verify exactly one business outcome. | 22, 37, 45-47 |
| 49 | Races Across Requests and Workers | Race invite-like seat claims/job dispatch with a synchronization harness; combine constraints, transactions, locks, and retry policy. | 23-24, 45, 48 |
| 50 | Logs, Correlation, and Operational Evidence | Emit structured request/job/dependency logs with correlation IDs, redact secrets, and reconstruct a failed cross-process trace. | 2, 17, 45-47 |
| 51 | Performance Across the Stack | Establish a budget, measure browser/API/SQL/queue timing, diagnose a seeded slow dashboard, change one bottleneck, and compare results. | 21, 39, 44-50 |

### Part VII — Prove That It Works

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 52 | Test Risk, Not Implementation Detail | Build a risk matrix for ticket creation and assign unit/integration/API/UI/E2E coverage; run the whole suite and identify gaps. | 38, 41 |
| 53 | Unit Tests for Business Decisions | Test status transitions, retry decisions, and pure frontend transforms with clocks/IDs injected rather than global mocks. | 16, 47, 52 |
| 54 | Database and Integration Tests | Test transactions, tenant queries, constraints, and jobs against MySQL; expose SQLite semantic drift and make isolation deterministic. | 22-24, 52 |
| 55 | API Contract and Feature Tests | Exercise auth, validation, serialization, pagination, and idempotency through HTTP; introduce a regression and read failure evidence. | 35-43, 48, 52 |
| 56 | React Component and Interaction Tests | Test user-visible list/form/error behaviors with Testing Library and a network stub; avoid asserting component internals. | 32, 39, 52 |
| 57 | End-to-End Tests for Critical Journeys | Use Playwright for login/create/assign, collect trace/screenshot on a seeded failure, and keep the suite intentionally small. | 38-43, 55-56 |
| 58 | The Full-Stack Debugging Method | Diagnose an unknown seeded incident using only symptom and evidence, produce a boundary timeline/root-cause report, fix, and add regression proof. | 50-57 |

### Part VIII — Ship It

| # | Proposed chapter | Build, inspect, or debugging lab | Depends |
| --- | --- | --- | --- |
| 59 | From Repository to Production | Build immutable app/worker images; run CI, migration, health/readiness, secrets and rollback drills; repair a broken env and failed migration rehearsal. | 51, 54-58 |
| 60 | Capstone: Team Access from Requirement to Production | Independently design and deliver invitations, role management, authorization and audit trail through a review rubric, adversarial tests, deployment plan, and observable measures. | All; especially 22-24, 35-50, 52-59 |

## 6. Curriculum changes and prerequisite rationale

The eight-part progression remains recognizable, but these changes prevent repetition and improve causality:

- Chapter 1 is renamed to emphasize boundaries and starts with a runnable trace rather than a survey of job titles.
- Chapter 11 explicitly distinguishes persistence models from domain decisions, preventing “fat model” from becoming the book's accidental architecture.
- Chapters 18/19 remain separate: 18 translates ORM behavior and finds N+1; 19 teaches relational result shape and aggregates. Their exercises, not terminology, justify both.
- Chapters 20/21 remain separate as measurement then plan interpretation. They should be merged during drafting if either cannot sustain its full lab.
- Chapter 40 teaches the chosen session/cookie authentication mechanism before authorization. Chapter 42 is reframed from a second mechanism chapter into security threats and token trade-offs, avoiding three repetitive authentication chapters.
- JSON serialization precedes REST semantics because readers need to see the representation before debating protocol behavior. API design stays after frontend architecture so UI outcomes inform the contract; earlier backend responses are explicitly provisional.
- Chapters 23 and 49 are differentiated: Chapter 23 is a two-transaction database lost update; Chapter 49 coordinates requests/workers and composes earlier transaction, constraint, queue, and idempotency tools. Rename rather than merge.
- Chapter 50 introduces observability before the formal test chapters so later failures generate useful artifacts. Chapter 58 synthesizes the method as a blind incident, rather than repeating logging theory.
- Chapter 59 is intentionally one substantial delivery chapter. If it becomes overloaded, trim platform-specific orchestration rather than adding chapters: production principles, a reproducible production-like run, CI, migrations, health, secrets, and rollback are the scope.

The dependency spine is `1-8 -> 9-17 -> 18/22/24 -> 35-43 -> 44-51 -> 52-59 -> 60`. Frontend Chapters 25-34 branch after Chapter 8 and join at Chapter 35. Within each part, readers may skip advanced detail only where the row's dependencies allow. Each chapter begins with “starting checkpoint” and “required chapters,” and its automated lab verifier detects the wrong checkpoint or seed state.

## 7. Chapter and lab contract

Each chapter should fit a substantial 60-120 minute core path plus optional extension and use this flexible contract:

1. State a business/engineering problem and observable acceptance criteria.
2. Draw the smallest useful mental model and name the boundaries involved.
3. Start from the canonical current application and establish a baseline command/result.
4. Make one coherent change; show important code, not generated boilerplate.
5. Activate a deterministic fault or expose a genuine implementation defect.
6. Capture evidence before editing: request, log, query, trace, state, or test failure.
7. Apply **Symptom -> Evidence -> Boundary -> Hypothesis -> Investigation -> Root Cause -> Fix -> Verification**.
8. Verify behavior and at least one failure path with an automated check or numerical comparison.
9. Connect the technical result to customer impact, security, cost, latency, or operator effort.

Every chapter specification must name: starting state, finish state, expected duration, commands, expected signal (not brittle full output), files touched, fault activation/reset, tests, cleanup, prerequisites, and extension. A chapter is not ready merely because its prose is complete; a fresh clone must pass its scripted walkthrough.

## 8. Debugging and controlled-failure design

### Recurring method

Every incident worksheet contains eight short fields:

- **Symptom:** observable deviation, stated without inferred cause.
- **Evidence:** timestamped facts and a known-good baseline.
- **Boundary:** browser/render, browser/network, HTTP/API, controller/domain, ORM/database, queue/cache, dependency, or runtime/infrastructure.
- **Hypothesis:** falsifiable explanation and predicted evidence.
- **Investigation:** cheapest discriminating experiment first.
- **Root cause:** mechanism plus the condition that activated it; not merely the bad line.
- **Fix:** smallest safe correction, including error behavior and data repair if necessary.
- **Verification:** regression test, repeated observation, and operational signal.

Readers keep a `debug-notes.md`-style worksheet outside tracked source. Solutions model discarded hypotheses so debugging is not portrayed as instant intuition.

### Fault harness

Prefer explicit, allow-listed lab controls over flaky timing or manual source corruption:

- `scripts/lab chapter NN setup|verify|reset` is the stable reader interface; it validates services and delegates to versioned scripts.
- A development/test-only fault profile (disabled and impossible to enable in production mode) selects named scenarios such as `api-delay`, `malformed-json`, `next-500`, `stale-cache`, or `job-fail-once`.
- `tools/fake-webhook` returns scripted sequences (delay, 429, 500, malformed JSON, success) from committed fixtures and records received requests without Internet access.
- Concurrency scripts use process barriers/advisory coordination, not “click quickly” or arbitrary sleeps, and print transaction/request IDs.
- Seed profiles (`tiny`, `two-tenants`, `performance`) use a fixed random seed and known expected counts. The performance profile records scale and requires relative comparisons, not universal millisecond thresholds.
- A fake clock and deterministic UUID provider are injected only where expiry/backoff/idempotency tests require them.
- Bug fixtures never weaken the normal application silently. Scenario activation is visible in the UI/logs, scoped to local/test environments, resettable, and verified as off by CI production checks.
- Each lab has an escape hatch: expected evidence, hints ordered by diagnostic depth, solution patch/reference, and a reset that preserves no hidden state.

Fault catalog metadata should record chapter, symptom, owning boundary, setup, evidence locations, reset, verifier, and platforms. This prevents many labs from collapsing into the same generic 500 error.

## 9. Repository architecture

Proposed end-state (created incrementally, never all as empty placeholders):

```text
/
├── README.md                  # five-minute orientation and canonical commands
├── CONTRIBUTING.md            # prose/code/lab contribution rules
├── Makefile                   # memorable aliases; delegates to scripts
├── compose.yaml               # reference development stack
├── .env.example               # documented safe defaults, no secrets
├── book/
│   ├── chapters/              # only when prose implementation begins
│   ├── appendices/            # setup, command reference, troubleshooting
│   └── assets/                # source diagrams and optimized exports
├── app/
│   ├── backend/               # conventional Laravel project, PHPUnit/Pest tests
│   └── frontend/              # Vite React/TS project and Vitest tests
├── labs/
│   ├── catalog.yaml           # lab/checkpoint/fault metadata
│   ├── fixtures/              # HTTP payloads and fault scenarios
│   └── solutions/             # focused diffs or guidance, not app copies
├── scripts/
│   ├── bootstrap              # validate tools, install, copy environment
│   ├── dev                    # start stack
│   ├── test                   # test tiers or all
│   ├── reset                  # migrations + deterministic tiny seed
│   └── lab                    # uniform chapter setup/verify/reset dispatcher
├── tools/
│   └── fake-webhook/          # smallest viable deterministic dependency
├── docs/
│   ├── BOOK_PLAN.md
│   ├── architecture/          # ADRs and architecture diagrams when needed
│   └── authoring/             # chapter/lab template and style guide
└── .github/workflows/         # lint, tests, image build, link/lab checks
```

`app/backend` and `app/frontend` retain framework conventions instead of inventing a cross-framework source hierarchy. Backend feature boundaries use Laravel namespaces/folders only after repeated complexity justifies them. Frontend is feature-oriented with a small `shared` area. Root scripts are POSIX shell with actionable errors; PowerShell wrappers are added only if tested on CI. `Makefile` targets (`setup`, `up`, `down`, `reset`, `test`, `lab CHAPTER=...`) are convenience, while the scripts remain directly runnable.

### Evolving code without sixty copies

`main` represents the latest stable application. Annotated tags/checkpoint commits such as `chapter-18-start` and `chapter-18-finish` provide history; do not store 60 full application snapshots or long-lived chapter branches. A release script and manifest map book edition + chapter to immutable commit SHA. Labs apply small, validated scenario setup to the checked-out start checkpoint. Solutions are focused diffs/explanations. CI periodically checks start-to-finish chapter paths, and published editions freeze dependency locks and checkpoint tags.

## 10. Testing and quality strategy

### Product tests

| Layer | Primary purpose | Tools/approach |
| --- | --- | --- |
| PHP unit | Domain transitions, retry policy, value decisions | PHPUnit or Laravel's default runner; fakes only at volatile boundaries |
| Database/integration | Eloquent scopes, MySQL constraints, transactions, jobs/cache adapters | Real MySQL/Redis containers; transaction/reset isolation |
| API feature/contract | HTTP semantics, validation, resources, auth/policies, tenant isolation | Laravel HTTP tests plus schema/example assertions |
| Type/static | DTO boundaries and PHP/TS errors | TypeScript strict mode; PHP formatter/static analysis introduced when actionable |
| React component | Accessible user interactions and state variants | Vitest, React Testing Library, Mock Service Worker or injected adapter |
| End-to-end | Few high-value cross-stack journeys | Playwright on the container stack, trace/screenshot/video only on failure |
| Operational smoke | image startup, health, migrations, worker/fake dependency | Compose production-like profile and scripted probes |

Use the same MySQL major version in development, CI, labs, and production examples. SQLite may illustrate semantic drift but is not the default backend test database. Tests own isolated databases/schema or uniquely named tenants, freeze time where relevant, and never depend on execution order. Authorization has a role/action matrix plus cross-tenant negative cases. Retry/idempotency tests assert outcomes and recorded attempts, not sleeps.

### Book and repository tests

- Format/lint Markdown, PHP, TypeScript, shell, YAML, and Dockerfiles with a deliberately small toolset.
- Check internal links, referenced files, command snippets, chapter numbering, and lab catalog schema.
- Execute canonical setup and smoke tests on supported CI platforms; nightly or release CI runs expensive performance/concurrency/E2E labs.
- Verify each chapter's start checkpoint, lab setup, expected failing signal, fix verifier, and reset. Failure activation may be asserted in a dedicated workflow so normal CI remains green.
- Build all images from lockfiles and scan for accidentally committed secrets; dependency/security updates are scheduled and tested, not floated during a reader session.
- Test diagrams/assets for references and accessibility text when those assets are eventually created.

The pyramid is risk-based, not quota-based. E2E tests cover login, ticket creation/assignment, and later invitation/role change; exhaustive combinations live lower. Coverage percentages are diagnostic, never the definition of correctness.

## 11. Tooling and dependencies

### Reference runtime

- Docker Engine with Compose v2 (Docker Desktop/WSL 2 acceptable) and Git.
- Supported PHP and Laravel versions, Composer, Node LTS, npm, and MySQL, pinned during scaffolding with committed `composer.lock`/`package-lock.json`.
- React, TypeScript strict mode, and Vite; native `fetch` before considering a client library.
- Redis only from Chapter 44 onward, shared by cache/queue with separate prefixes or logical configuration.
- Laravel queue worker; database queue may be shown conceptually before Redis but only one reference configuration should remain.
- PHPUnit (or the version bundled by Laravel), Vitest + Testing Library, and Playwright.
- Laravel Pint, ESLint/Prettier (or a single compatible JS formatter), ShellCheck, and a Markdown/link checker. Add PHP static analysis only after configuration proves high signal.
- Structured JSON logging to stdout in production mode; application health endpoints and correlation IDs. Do not introduce a hosted observability vendor as a requirement.
- GitHub Actions as the initial CI example. Deployment remains platform-neutral and uses images/artifacts rather than adding Kubernetes or Terraform.

### Operational budgets

Publish minimum CPU/RAM/disk estimates and a reduced profile before implementation. Keep the default stack to web, frontend dev server, MySQL, and later Redis/worker/fake-webhook. Add services via Compose profiles so early chapters do not pay the resource or conceptual cost. All exercises must work without paid accounts or external network calls after dependency installation.

## 12. Capstone design

### Business brief

RelayDesk customers need administrators to invite colleagues to their organization, assign an appropriate role, change access later, and review who changed permissions and when. Invites must expire, cannot grant privileges the inviter lacks, and must not create duplicate membership when requests/jobs are retried.

### Constraints and deliberate ambiguity

Provide stakeholder statements, existing architecture, role capability matrix, security constraints, example emails, and measurable acceptance outcomes—but no endpoint list, schema, component tree, or line-by-line steps. Learners must clarify decisions such as re-invitation, revocation, expired links, already-registered email, self-demotion, last-owner protection, and whether audit history is tenant-visible.

### Required evidence package

1. Refined requirements, abuse cases, acceptance criteria, and a small architecture/data-flow decision record.
2. Migration/schema constraints and safe rollout/backfill/rollback plan.
3. REST contract and error semantics, including token handling and idempotency.
4. Authenticated/authorized Laravel implementation with transactional membership + audit change.
5. Accessible React invitation, role-management, pending/error/expired, and audit views.
6. Email queued through a fake transport; retry behavior produces one usable invitation/effect.
7. Unit, MySQL integration, API authorization matrix, component, and one critical Playwright journey.
8. Deployment runbook, structured audit/request logs, counters for invite sent/accepted/failed, and alert/diagnostic queries.
9. A debug report for a supplied duplicate-acceptance or privilege-escalation fault using the eight-step method.

### Assessment

Rubric weights favor correctness/security and evidence over code volume: requirements 10%, data/integrity 15%, authorization/security 20%, backend/API 15%, frontend/UX 10%, tests 15%, operations/observability 10%, engineering explanation 5%. Automated adversarial tests check cross-tenant access, expiration, duplicate acceptance, unauthorized role elevation, last-owner invariants, secret redaction, and audit atomicity. Multiple sound designs should pass documented outcomes.

## 13. Phased implementation plan

### Phase 0 — Decisions and reproducibility contract

- Resolve the open decisions below; record stack/version and architecture ADRs.
- Define supported host matrix, resource budget, chapter/lab schema, authoring template, and definition of done.
- Add root README, contribution guide, bootstrap diagnostics, CI skeleton, and dependency update policy.
- **Exit:** a fresh machine can run diagnostics and contributors can validate a minimal documentation change.

### Phase 1 — Walking skeleton (Chapters 1-8)

- Build the disposable minimal slice, baseline containers, deterministic seed, request ID, and first fault scenarios.
- Draft only Part I and test every command on clean environments; learn whether 60-120 minutes is realistic.
- **Exit:** novice pilot can trace, break, repair, reset, and explain one browser-to-row request.

### Phase 2 — RelayDesk backend and data (9-24)

- Scaffold Laravel and schema/factories; implement minimal ticket/customer/project behavior and MySQL evidence labs.
- Build deterministic data generator and concurrency barrier; establish PHP/unit/integration/API CI tiers.
- **Exit:** backend vertical slice and database labs pass from every published checkpoint.

### Phase 3 — Frontend and contract integration (25-43)

- Build fixture-backed React/TS UI, then freeze the first API contract and integrate it.
- Add session auth, policies, tenant isolation, browser security labs, component/API tests.
- **Exit:** authenticated ticket journey works and fails accessibly; security matrix and contract tests pass.

### Phase 4 — Resilience and evidence (44-51)

- Introduce profiled Redis/worker and fake webhook only now.
- Implement scripted dependency failures, retry/idempotency/cache/race labs, structured correlation, and performance baselines.
- **Exit:** each production-behavior lab is deterministic in repeated CI runs and identifies distinct evidence/boundaries.

### Phase 5 — Verification and delivery (52-59)

- Rationalize test pyramid, add a small Playwright suite, incident lab, production image/profile, CI delivery, migration and rollback drills.
- Perform security, accessibility, cross-platform, clean-clone, and secret-redaction reviews.
- **Exit:** a release candidate runs from lockfiles, passes the documented pipeline, and survives deployment drills.

### Phase 6 — Capstone and publication hardening (60)

- Create brief, fixtures, rubric, hidden/adversarial tests, reference solution, and instructor notes.
- Pilot without the solution; revise ambiguity that tests architecture trivia rather than outcomes.
- Freeze edition checkpoint SHAs, verify links/assets/licenses, and publish errata/version policy.
- **Exit:** representative readers independently ship and diagnose the feature with defensible evidence.

Implementation should proceed vertical slice by vertical slice, not “write all prose, then build all code.” At each phase, application behavior, lab, verifier, prose, and checkpoint ship together.

## 14. Risks and guardrails

| Risk | Consequence | Guardrail / measurable check |
| --- | --- | --- |
| SaaS scope creep | Book becomes product construction rather than engineering instruction. | Fixed entity/out-of-scope list; new feature needs a named learning outcome and replaces rather than adds scope. |
| Sixty shallow glossary chapters | Readers recognize terms but cannot diagnose systems. | Every chapter has observable artifact, controlled fault or measurement, verification, and business consequence. |
| Repeated authentication/concurrency/API material | Bloated prose and blurred mental models. | Enforce distinctions in Section 6; merge at outline review if labs produce the same evidence. |
| Version drift | Commands/checkpoints stop working. | Pin images/lockfiles per edition, automated clean builds, scheduled upgrade branches, explicit support window. |
| Sixty application copies/branches | Fixes diverge and repository becomes huge. | One Git history with immutable checkpoint tags/manifests; focused solution diffs only. |
| Flaky timing/concurrency/performance labs | Readers blame themselves or platforms. | Barriers/scripted responses/fake clock/fixed seed; relative assertions; repeated nightly runs. |
| Docker hides fundamentals | Readers can operate recipes but not processes/networks. | Early process/port/DNS labs and visible commands; Docker explained as orchestration, not magic. |
| Infrastructure overload | Hardware burden distracts from app reasoning. | Modular monolith, Compose profiles, Redis only when used, no Kubernetes/hosted vendor requirement. |
| Security shortcuts become copied patterns | Tenant leaks or unsafe token handling. | Deny-by-default policies, two-tenant negative tests, threat chapter, redaction tests, security review gates. |
| Test suite too slow | Readers skip verification. | Fast chapter verifier, tiered commands, limited E2E, published expected durations, nightly heavy suite. |
| Platform incompatibility | Setup consumes learning time. | Supported OS matrix in CI, preflight diagnostics, normalized line endings, tested WSL path, escape-hatch troubleshooting. |
| Hidden state between chapters | Labs only work for authors. | Idempotent setup/reset, state assertions, checkpoint SHA check, clean-clone CI. |
| Prose and code diverge | Incorrect commands erode trust. | Extract/execute snippets where possible; release checklist validates every command and reference. |
| Capstone becomes recipe or guessing game | No independent engineering judgment. | Outcome-based brief/rubric, stated constraints, multiple accepted designs, hidden adversarial behaviors. |

Additional hard limits: no new runtime service without a chapter experiment and removal analysis; no new dependency without an ADR-style justification (problem, alternatives, maintenance cost); no chapter over its time budget without trimming or explicitly marked extension; and no production claim based solely on a happy-path screenshot.

## 15. Open decisions before implementation

1. Which supported Laravel/PHP, MySQL, Node, and browser versions will define edition 1, and for how long will security/command updates be maintained?
2. Is Docker mandatory or merely the reference path, and which Windows/macOS/Linux configurations will CI and maintainers actually support?
3. Should the backend test style use plain PHPUnit or Pest? Choose one based on Laravel support and teaching clarity; do not teach both.
4. Which Markdown/book build system will produce web/PDF output? Select only after testing code callouts, links, accessibility, and executable-snippet integration.
5. Will the repository be public from the first scaffold, and what licenses apply separately to code and prose/assets?
6. How are checkpoint tags published and corrected after an edition release without silently moving tags? Prefer immutable tags plus an errata patch mapping.
7. What minimum hardware/resource budget is acceptable, and does a reduced Compose profile need to be a supported path?
8. Should API examples maintain a formal OpenAPI document or lightweight executable schemas? The answer must minimize duplicate sources of truth.
9. Which fake email interface accompanies the webhook simulator (log driver, local inbox UI, or recorded adapter), and can it avoid another always-on service?
10. What accessibility baseline (recommended: WCAG 2.2 AA for implemented workflows) and automated/manual checks are part of chapter completion?
11. What data privacy/redaction policy governs fixtures, screenshots, logs, and audit before any content is generated?
12. Who pilots each phase, and what signals—completion time, reset failures, diagnostic accuracy, and test flakiness—cause scope revision?

## 16. Implementation status

The primary curriculum is complete through Chapter 60. Chapter 59 is backed by a versioned multi-stage image, explicit production-like Compose process model, safe configuration example, CI gate, liveness/readiness routes, migration/deployment verification, backup/restore scripts, and controlled rollback/incident exercises. Chapter 60 deliberately remains an independently implemented learner feature: its deliverable is a declared contract and evidence package assessed through the published rubric, with only a separate instructor strategy. This preserves the planned capstone outcome without turning it into Chapter 61-style scaffolding.

Part VII (Chapters 52–58) is implemented. The accumulated PHPUnit and React suites are retained and organized by the evidence each level can provide. Focused commands separate unit, database/integration, API, component, and Playwright evidence. Two browser journeys cover persisted admin creation and restricted-viewer presentation. A neutral, development-only `debug-capstone` profile deterministically combines duplicate, stale-cache, and missing-background-work symptoms; the reader worksheet is separate from instructor root-cause notes and focused regression tests preserve the repairs.

Part VI (Chapters 44–51) remains implemented. A tenant-keyed database cache makes stale copies and targeted invalidation inspectable. Ticket creation transactionally records an idempotency result and integration delivery, then dispatches durable database-queued work after commit. A repository-owned HTTP simulator supplies deterministic success, delay, transient/persistent failure, malformed, and 4xx modes. Explicit timeouts/retry classification, job exhaustion, correlation relationships, structured safe logs, an atomic unique-claim race, and relative performance evidence extend the same modular monolith.

The implementation uses Laravel's database cache and queue rather than the plan's provisional Redis target. This is a deliberate infrastructure reduction: current educational volume does not justify another datastore, while MySQL tables let learners inspect cached copies, queue ownership, and failed work using established tools. Redis remains an architecture alternative, not a synonym for either concern.

This implementation deliberately uses Laravel's built-in session guard rather than adding Sanctum: the repository needs no token features, and the same stateful identity/cookie mechanics remain visible. Before public deployment, add framework CSRF protection/Sanctum's SPA flow, login rate limiting, HTTPS-only cookies, and a production session store. Those are recorded risks, not claims that this local teaching stack is Internet-ready.

## 17. Recommended next task

Perform a final repository audit rather than beginning another part: run every documented command on a clean supported machine, security-review authentication/secrets/redaction and the production-like boundary, pilot Chapters 59–60 with learners, check accessibility and prose/navigation consistency, and record reproducibility/flakiness evidence. Resolve the edition-level open decisions above before calling the repository internet-production-ready.
