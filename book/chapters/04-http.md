# 4. HTTP: The Language Between Systems

[Previous](03-browser-runtime.md) · [Next](05-addressing.md)

HTTP messages expose a method, target URL, headers, optional body, status, response headers, and response body. Observe them rather than memorizing status-code lists.

## Compare requests

```sh
curl -v http://localhost:8080/api/tickets
curl -i -X POST -H 'Content-Type: application/json' \
  -d '{"subject":"Read the wire contract"}' http://localhost:8080/api/tickets
curl -i -X PUT http://localhost:8080/api/tickets
```

In verbose output, lines beginning `>` are request evidence and `<` are response evidence. GET returns `200`; valid POST returns `201` and `Location`; PUT returns `405` and `Allow`. The last response proves PHP received and interpreted the request—it is not a connection failure.

Now omit the content type:

```sh
curl -i -d '{"subject":"Invisible contract"}' http://localhost:8080/api/tickets
```

`curl -d` defaults to form encoding, so the endpoint returns `415`. Retry with `-H 'Content-Type: application/json'`. Then send malformed JSON with that header and observe `400`; send `{"subject":""}` and observe `422`. These statuses distinguish unsupported representation, malformed syntax, and invalid application input in this API.

In DevTools Network, create a ticket and inspect **Headers**, **Payload**, **Response**, and **Timing**. Verify browser and curl produce equivalent POST messages. See [RFC 9110](https://www.rfc-editor.org/rfc/rfc9110) for HTTP semantics and [MDN's HTTP overview](https://developer.mozilla.org/en-US/docs/Web/HTTP/Overview) for a browser-oriented reference.
