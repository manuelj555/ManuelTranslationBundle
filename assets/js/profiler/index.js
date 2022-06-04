import React from "react";
import {createRoot} from "react-dom/client";
import App from "./components/App";

(() => {
    const tabContents = document.querySelectorAll('.sf-tabs .tab-content');

    if (tabContents.length === 0) {
        return;
    }

    const tabContent = [...tabContents].pop();

    if (!tabContent) {
        return;
    }

    const reactContainer = document.createElement('div');
    tabContent.appendChild(reactContainer);

    const reactRoot = createRoot(reactContainer);
    reactRoot.render(
        <React.StrictMode>
            <App />
        </React.StrictMode>
    );
})();
