# Debugging evidence record

Copy this section once per defect. Record observations before explanations.

**Symptom:** What is visibly wrong?

**Evidence:** Include request/correlation IDs, relevant status/body/log/SQL/DOM observations, and commands.

**Boundary:** Last known-good state → first known-bad state.

**Hypothesis:** One falsifiable explanation.

**Experiment:** The smallest test or observation that would confirm or refute it.

**Root cause:** The mechanism—not merely the file changed.

**Fix:** The smallest justified change.

**Verification:** Reproduction no longer fails; name the focused regression test and broader suite rerun.

Check value, boundary, ownership, timing, ordering, and count. Note what each item of evidence does **not** prove.
