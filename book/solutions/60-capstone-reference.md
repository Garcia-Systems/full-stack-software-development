# Chapter 60 instructor reference strategy

This is a review aid, not the reader path and not a single mandated schema or endpoint set. The repository intentionally does **not** ship completed invitation functionality: the capstone measures independent engineering, and no hidden acceptance test requires exact class names.

A defensible solution usually stores a random token only as a one-way digest, locks the invitation row in a database transaction, verifies email/state/expiry, creates or updates one tenant membership under a unique constraint, marks acceptance once, and appends a safe audit event in the same transaction. Creation commits invitation plus audit state before dispatching a database-queue job; provider failure changes diagnosable delivery state rather than deleting the invitation. Authorization scopes every administrative lookup through active membership and an explicit role policy.

The reviewer should evaluate invariants using the public contract declared in the student's report. In particular, release two real MySQL transactions from a barrier and require one accepted transition, one membership, one acceptance audit event, and a stable conflict/already-completed result for the loser. Inspect storage and logs to prove the raw token appears only in the one-time delivery boundary. Run the rubric's adversarial checks rather than comparing file structure.
