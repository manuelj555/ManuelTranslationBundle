import React from "react";
import Filter from "./translation/Filter";
import List from "./translation/List";
import {TranslationsProvider} from "../context/TranslationsContext";

export default function App() {
    return (
        <div>
            <TranslationsProvider>
                <Filter/>
                <List/>
            </TranslationsProvider>
        </div>
    );
}