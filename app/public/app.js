const list = document.querySelector('#tickets');
const status = document.querySelector('#status');
const form = document.querySelector('#ticket-form');

function render(tickets) {
  list.replaceChildren(...tickets.map(({ id, subject }) => {
    const item = document.createElement('li');
    item.textContent = `#${id} — ${subject}`;
    return item;
  }));
}
async function request(path, options = {}) {
  const response = await fetch(path, options);
  const requestId = response.headers.get('X-Request-ID');
  sessionStorage.setItem('lastRequestId', requestId || 'missing');
  console.info('RelayDesk response', { status: response.status, requestId });
  const data = await response.json();
  if (!response.ok) throw new Error(`${response.status}: ${data.message || data.error || 'Request failed'}`);
  return data;
}
async function load() {
  try { const data = await request('/api/tickets'); render(data.tickets); status.textContent = `${data.tickets.length} ticket(s)`; }
  catch (error) { status.textContent = error.message; console.error(error); }
}
form.addEventListener('submit', async event => {
  event.preventDefault(); status.textContent = 'Saving…';
  try {
    await request('/api/tickets', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ organization_id: 1, customer_id: 1, subject: form.elements.subject.value }) });
    form.reset(); await load();
  } catch (error) { status.textContent = error.message; console.error(error); }
});
if (new URLSearchParams(location.search).has('browser_fault')) missingBrowserFunction();
load();
