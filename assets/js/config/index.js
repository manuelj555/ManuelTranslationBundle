import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";
import {GlobalsProvider} from "./context/GlobalsContext";

const container = document.getElementById('translations-configuration');
const paths = JSON.parse(container.dataset.paths || '{}');
const domains = JSON.parse(container.dataset.domains || '[]');
const locales = JSON.parse(container.dataset.locales || '[]');

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <GlobalsProvider paths={paths} domains={domains} locales={locales}>
            <App/>
        </GlobalsProvider>
    </React.StrictMode>
);