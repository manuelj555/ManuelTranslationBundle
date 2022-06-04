import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";

const container = document.getElementById('resolve-conflicts-container');
const items = JSON.parse(container.dataset.items || '[]');
const resolveConflictsPath = container.dataset.endpoint || '';

console.log(items);

const root = createRoot(container);

root.render(
    <React.StrictMode>
        <App
            defaultItems={items}
            endpoint={resolveConflictsPath}
        />
    </React.StrictMode>
);