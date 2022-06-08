import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";
import {GlobalsProvider} from "./context/GlobalsContext";
import {QueryClient, QueryClientProvider} from "react-query"
import {ReactQueryDevtools} from "react-query/devtools";

const container = document.getElementById('translations-configuration');
const paths = JSON.parse(container.dataset.paths || '{}');
const domains = JSON.parse(container.dataset.domains || '[]');
const locales = JSON.parse(container.dataset.locales || '[]');

const queryClient = new QueryClient({
    defaultOptions: {
        queries: {
            refetchOnWindowFocus: false,
        }
    }
})

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <QueryClientProvider client={queryClient}>
            <GlobalsProvider paths={paths} domains={domains} locales={locales}>
                <App/>
            </GlobalsProvider>
            <ReactQueryDevtools />
        </QueryClientProvider>
    </React.StrictMode>
);