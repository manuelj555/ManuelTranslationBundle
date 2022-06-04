import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";

const container = document.getElementById('resolve-conflicts-container');
const items = JSON.parse(container.dataset.items || '[]');
// const paths = JSON.parse(container.dataset.paths || '{}');
// const domains = JSON.parse(container.dataset.domains || '[]');
// const locales = JSON.parse(container.dataset.locales || '[]');

console.log(items);

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <App defaultItems={items}/>
    </React.StrictMode>
);