# 32. Forms and Validation

[Previous](31-hooks-and-effects.md) · [Next](33-routing-and-navigation.md)

The “New ticket” view uses an existing RelayDesk entity. Each controlled input receives its value from React state and reports changes through an event. Submission prevents browser navigation, validates the draft, exposes pending feedback, calls the injected local adapter, and renders success/failure.

Try three deterministic cases: submit empty fields; choose a customer but enter `abc`; then submit “Printer needs attention.” Field messages and `aria-invalid` identify local problems, the button communicates pending state, and the status region announces the outcome. Inactive customers cannot be selected.

The three protections remain distinct:

- **frontend validation improves interaction** and can be bypassed;
- **backend validation protects the application boundary** when Part V connects it;
- **database constraints protect persistent truth** for every writer.

Part IV deliberately creates only a local fixture record. It includes a placeholder catch path for future server errors without inventing the API contract early. Do not interpret success as a MySQL write; reload resets in-memory state.

Controlled failure: remove the `return` after setting field errors. The adapter receives an invalid draft despite correct visible messages. Put a breakpoint in `submit`, inspect `next`, and prove no network request exists. Restore the early return and run the form tests. See React's [input reference](https://react.dev/reference/react-dom/components/input) and MDN's [client-side validation warning](https://developer.mozilla.org/en-US/docs/Learn_web_development/Extensions/Forms/Form_validation).
