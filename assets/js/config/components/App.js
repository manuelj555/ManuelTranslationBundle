import React, {useContext} from "react";
import Filter from "./translation/Filter";
import List, {LoadingList} from "./translation/List";
import {TranslationsProvider} from "../context/TranslationsContext";
import LoadingContext from "../context/LoadingContext";

export default function App() {
    const {appLoading} = useContext(LoadingContext);

    return (
        <div>
            <TranslationsProvider>
                <Filter/>
                {appLoading
                    ? <LoadingList/>
                    : <List/>
                }
            </TranslationsProvider>
        </div>
    );
}