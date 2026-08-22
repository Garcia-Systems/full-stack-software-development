# Chapter 37: REST and HTTP Semantics

Previous: [Chapter 36](./36-json-and-serialization.md)

## Start with the business boundary

Methods express intent: GET reads, POST creates, PATCH changes selected fields, and DELETE removes. Actual behavior supplies meaning: list/show are `200`, create is `201` plus `Location`, and successful delete is `204` with no body. Do not write a client that parses JSON after `204`.

## Make the boundary visible

Classify evidence rather than memorize: `400` means the server cannot accept the request at its syntax/protocol boundary; `401` needs valid authentication; `403` knows the identity but denies it; `404` has no addressable resource; `409` conflicts with current state; Laravel validation uses `422`; `500` is an unexpected server failure. A network exception has no HTTP status at all.

## Evidence checklist

At each boundary record: event/state; method and URL; request headers, cookie, and JSON body; route, request ID, identity, validation and authorization; SQL/row effect; response status, headers and JSON; final React state/render. An explanation without an artifact is still a hypothesis.

## Controlled lab and verification

```sh
curl -i -X DELETE -b /tmp/relaydesk.cookies http://localhost:8080/api/v1/tickets/999999
```

Predict the observation first, run it, save the status/body and `X-Request-ID`, then inspect the matching log and database row. Reset with `make reset`; never use lab controls outside local/testing.

## References

- [HTTP Semantics (RFC 9110)](https://www.rfc-editor.org/rfc/rfc9110)
- [MDN: HTTP response status codes](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Status)
- [Laravel: HTTP responses](https://laravel.com/docs/12.x/responses)
- [Laravel: authentication](https://laravel.com/docs/12.x/authentication)
- [Laravel: authorization](https://laravel.com/docs/12.x/authorization)
- [MDN: CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CORS)
- [OWASP Session Management Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html)

## Exit proof

Explain what crossed every boundary and identify which artifact would distinguish HTTP rejection, application rejection, and no completed request. Continue to the next chapter only when the normal suite remains green.

Next: [Chapter 38](./38-react-talks-to-laravel.md)
