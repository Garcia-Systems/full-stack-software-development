// Chapter 25: paste into DevTools on the legacy/server-rendered customer list.
const query = document.querySelector("#customer-filter");
const rows = [...document.querySelectorAll("[data-customer-name]")];
query?.addEventListener("input", (event) => {
  const value = event.currentTarget.value.toLowerCase();
  rows.forEach((row) => {
    row.hidden = !row.dataset.customerName.toLowerCase().includes(value);
  });
  console.info("filter event", {
    value,
    visible: rows.filter((row) => !row.hidden).length,
  });
});
// Controlled failure: change "input" to "change". The backend HTML remains correct,
// but the handler will not run after every keystroke.
