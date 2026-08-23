# Chapter 59 instructor notes: production incident

Keep this separate from the symptom. Stop `worker` and set its `DEPENDENCY_URL` to `http://dependency:9999`; leave web healthy. Ticket creation commits and returns successfully, but delivery stays queued while the worker is absent. Starting the worker moves evidence to retry/failure because the dependency port is wrong. This intentionally contains two boundaries so “start worker” is not accepted as complete verification.

A defensible investigation proves web liveness/readiness/version, sees the durable job and pending delivery, notices no active worker, starts it, correlates the resulting dependency connection failures, compares runtime configuration to the simulator port, repairs configuration, recreates the worker, retries failed work if necessary, and proves one delivered effect. Reset to the documented `.env.production` afterward.
