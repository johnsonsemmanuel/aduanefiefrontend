import { useState } from "react";
import { QueryClient, QueryClientProvider } from "react-query";
import { config } from "constants/reactQuery.config";

export default function ClientQueryClientProvider({
  children,
}: {
  children: React.ReactNode;
}) {
  const [queryClient] = useState(() => new QueryClient(config));
  return (
    <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
  );
}
