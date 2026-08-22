# Chapter 39: Loading, Errors, and Empty States

Previous: [Chapter 38](./38-react-talks-to-laravel.md)

## Start with the business boundary

The UI models idle, loading, success-with-data, empty, and error rather than pretending a Promise is instant. The API client separately types validation, unauthenticated, forbidden, not-found, conflict, unexpected, and network failures. An HTTP error completed with a response; a network error has no response status.

## Make the boundary visible

With `LAB_FAULTS=true`, send `X-RelayDesk-Lab: delay`, `empty`, or `server-error`. Controls run only in local/testing and are inert otherwise. Delay is fixed, empty returns a successful empty collection, and failure is safely rendered while detail remains server-side. AbortController prevents an obsolete load from winning—a Part IV race lesson now applied to HTTP.

## Evidence checklist

At each boundary record: event/state; method and URL; request headers, cookie, and JSON body; route, request ID, identity, validation and authorization; SQL/row effect; response status, headers and JSON; final React state/render. An explanation without an artifact is still a hypothesis.

## Controlled lab and verification

```sh
curl -i -H 'X-RelayDesk-Lab: empty' -b /tmp/relaydesk.cookies 'http://localhost:8080/api/v1/tickets?organization_id=1'
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

Next: [Chapter 40](./40-authentication.md)
