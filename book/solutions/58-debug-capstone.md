# Chapter 58 instructor notes: debug-capstone

Do not link this page from symptom details beyond the final solution gate.

The neutral `debug-capstone` profile is interpreted only by development/testing lab middleware. It creates three independent boundary defects in ticket creation: the incoming idempotency key is not used (duplicate rows); dashboard invalidation is omitted (the cache retains the pre-create count); and delivery dispatch is omitted (a delivery row remains pending but no queue record exists).

A sound investigation distinguishes them with counts: two identical-key POSTs yield distinct ticket IDs; dashboard stays a cache hit with its old active count despite a new row; and `integration_deliveries` contains pending records while `jobs` and simulator deliveries remain empty. Request and job IDs correlate HTTP, logs, and rows.

The repair is to restore normal `ApiTicketController::store` behavior for each branch: use and atomically persist the validated key, invalidate the organization-specific dashboard key after commit, and dispatch exactly one job inside the idempotent business outcome. Do not remove the profile mechanism from the teaching repository.

Regression mapping: `CapstoneProfileTest` and `PartSixResilienceTest::test_same_idempotency_key_creates_and_dispatches_once` protect count/idempotency/dispatch; `test_dashboard_cache_is_tenant_keyed_and_invalidation_changes_value` protects cache ownership; `test_job_validates_success_and_records_failure` protects synchronous job effect with a fake dependency. Rerun API tests, then the complete clean MySQL suite and E2E journeys.
