import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";
import {GlobalsResolver} from "./context/GlobalsContext";

const container = document.getElementById('translations-configuration');
const paths = JSON.parse(container.dataset.paths || '{}');
const domains = JSON.parse(container.dataset.domains || '[]');

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <GlobalsResolver paths={paths} domains={domains}>
            <App/>
        </GlobalsResolver>
    </React.StrictMode>
);