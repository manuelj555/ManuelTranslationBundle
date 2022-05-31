import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";
import {GlobalsProvider} from "./context/GlobalsContext";
import {LoadingProvider} from "./context/LoadingContext";

const container = document.getElementById('translations-configuration');
const paths = JSON.parse(container.dataset.paths || '{}');
const domains = JSON.parse(container.dataset.domains || '[]');

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <GlobalsProvider paths={paths} domains={domains}>
            <LoadingProvider>
                <App/>
            </LoadingProvider>
        </GlobalsProvider>
    </React.StrictMode>
);