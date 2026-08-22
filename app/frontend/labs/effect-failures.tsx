import { useEffect, useState } from "react";
export function DocumentTicketTitle({ subject }: { subject: string }) {
  const [selected, setSelected] = useState(subject);
  useEffect(() => {
    document.title = `${selected} · RelayDesk`;
    return () => {
      document.title = "RelayDesk";
    };
  }, [selected]);
  return (
    <button onClick={() => setSelected(subject)}>Synchronize title</button>
  );
}
// Controlled failures: omit `selected` (stale title), add a state write on every
// execution (loop), or omit cleanup (external state leaks after unmount).
