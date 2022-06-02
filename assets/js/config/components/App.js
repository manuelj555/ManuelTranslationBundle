import React, {useContext} from "react";
import Filter from "./translation/Filter";
import List, {LoadingList} from "./translation/List";
import TranslationsContext from "../context/TranslationsContext";
import LoadingContext from "../context/LoadingContext";

export default function App() {
    const {appLoading} = useContext(LoadingContext);
    const {applyFilters} = useContext(TranslationsContext);

    return (
        <div>
            <Filter onSubmit={applyFilters}/>
            {appLoading
                ? <LoadingList/>
                : <List/>
            }
        </div>
    );
}