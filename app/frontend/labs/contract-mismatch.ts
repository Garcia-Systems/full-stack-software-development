interface CustomerSummary {
  id: number;
  name: string;
  email?: string;
}
function heading(customer: CustomerSummary): string {
  return customer.name.toUpperCase();
}
const wrongShape = { id: 7, displayName: "Northwind Studio" };
// Controlled failure: uncomment this line and run npm run typecheck.
// heading(wrongShape);
heading({ id: wrongShape.id, name: wrongShape.displayName });
