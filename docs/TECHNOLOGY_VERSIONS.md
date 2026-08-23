# Technology and version baseline

This document separates the edition's declared compatibility from an accidental host installation. Direct frontend packages are exact in `app/package.json`; Composer constraints are compatible ranges. The container images define the reference runtime.

| Technology | Repository baseline | Source of truth |
| --- | --- | --- |
| PHP | 8.4 container; application accepts `^8.3` | `app/Dockerfile`, `app/composer.json` |
| Laravel | 12.x | `app/composer.json` |
| MySQL | 8.4 | both Compose files and CI |
| Node | 20 (README requires 20.19+) | `app/Dockerfile`, CI, README |
| React / React DOM | 19.1.1 | `app/package.json` |
| TypeScript | 5.9.2 | `app/package.json` |
| Vite | 7.1.3 | `app/package.json` |
| PHPUnit | 11.5-compatible | `app/composer.json` |
| Vitest | 3.2.4 | `app/package.json` |
| Playwright | 1.55.0 | `app/package.json` |
| Cache / queue | Laravel database drivers on MySQL (not Redis) | Compose environment and Chapter 59 |
| Docker Compose | Compose v2 | `scripts/bootstrap` |

## Reproducibility warning

Neither the npm lockfile (`package-lock.json`) nor the Composer lockfile (`composer.lock`) is currently committed under the application directory. Exact direct npm versions still have transitive dependencies, and Composer's compatible ranges intentionally permit multiple versions. Therefore this baseline describes compatibility, not a byte-for-byte dependency graph. Producing, reviewing, and enforcing both lockfiles is a release blocker, not optional polish. After adding them, use `npm ci` and `composer install` from the locks in CI and image builds.

Framework-specific chapter statements should be read against this baseline. General HTTP, browser, SQL, and concurrency claims do not depend on Laravel conventions unless the chapter says so explicitly.
