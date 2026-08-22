# 27. Why TypeScript Exists

[Previous](26-asynchronous-javascript.md) · [Next](28-reacts-mental-model.md)

JavaScript permits an object shaped as `{ displayName: "Northwind" }` to reach code that reads `customer.name.toUpperCase()`. The interface mismatch becomes `undefined` and fails only on the executed path. TypeScript moves that expectation to a checkable boundary.

Inspect `app/frontend/labs/contract-mismatch.ts`. Uncomment `heading(wrongShape)` and run:

```sh
cd app
npm run typecheck
```

The compiler reports that `name` is missing. Fixing the contract deliberately—mapping `displayName` to `name`—makes the check pass. Do not use `as CustomerSummary` or `any`: those tell the compiler to stop protecting the exact uncertainty under investigation.

RelayDesk's useful vocabulary lives in `app/frontend/src/types.ts`: primitives describe IDs and labels; interfaces describe customer/ticket object shapes; `Ticket[]` is an array; `description?` and `email?` are optional; function parameters and returns document adapter behavior. Literal unions constrain `status`, `priority`, and async state to known alternatives. Narrowing `state.status` proves which fields exist without assertions.

Types are erased from the browser build. They cannot validate an untrusted HTTP response at runtime, cannot repair incorrect business rules, and cannot guarantee database truth. They make code-to-code expectations explicit. Part V will address the real JSON boundary; Part IV's typed fixture adapter keeps that curriculum visible rather than pretending local objects are an API.

See the TypeScript handbook on [everyday types](https://www.typescriptlang.org/docs/handbook/2/everyday-types.html) and [object types](https://www.typescriptlang.org/docs/handbook/2/objects.html).
