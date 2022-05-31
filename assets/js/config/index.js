import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";
import {GlobalsResolver} from "./context/GlobalsContext";

const container = document.getElementById('translations-configuration');
const paths = JSON.parse(container.dataset.paths || '{}');

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <GlobalsResolver paths={paths}>
            <App/>
        </GlobalsResolver>
    </React.StrictMode>
);