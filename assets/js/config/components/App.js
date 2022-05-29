import React from "react";
import TranslationsFilter from "./TranslationsFilter";
import TranslationsList from "./TranslationsList";


export default function App () {
    return (
        <div>
            <TranslationsFilter />
            <TranslationsList />
        </div>
    );
}