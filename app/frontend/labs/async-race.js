const wait = (value, milliseconds) =>
  new Promise((resolve) => setTimeout(() => resolve(value), milliseconds));
let latestRequest = 0;
async function search(label, milliseconds) {
  const request = ++latestRequest;
  console.time(`request-${request}-${label}`);
  const result = await wait(label, milliseconds);
  console.timeEnd(`request-${request}-${label}`);
  if (request !== latestRequest) {
    console.info("ignored stale result", result);
    return;
  }
  document.querySelector("#async-result").textContent = result;
}
search("first/slow", 400);
search("second/fast", 100);
// Controlled failure: remove the latestRequest comparison. The correct slow value
// arrives last and incorrectly replaces the newer intent, every time.
