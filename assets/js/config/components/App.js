import React, {useMemo} from "react";
import Filter from "./translation/Filter";
import List, {LoadingList} from "./translation/List";
import Item from "./translation/Item";
import useTranslations from "../hooks/useTranslations";

export default function App() {
    const {
        translations,
        isLoading,
        config,
        setConfig,
        translationActions,
    } = useTranslations();

    const [applyFilters, changePage] = useMemo(() => {
        const applyFilters = (filters) => setConfig({filters});
        const changePage = (page) => setConfig({page});

        return [applyFilters, changePage];
    }, [setConfig]);

    return (
        <div>
            <Filter onSubmit={applyFilters}/>
            {isLoading
                ? <LoadingList/>

                : (
                    <List
                        addEmptyItem={translationActions.addEmptyItem}
                        paginationData={config.pagination}
                        changePage={changePage}
                    >
                        {translations.map(translation => (
                            <Item
                                key={translation.uuid}
                                translation={translation}
                                saveItem={translationActions.saveItem}
                                removeEmptyItem={translationActions.removeEmptyItem}
                            />
                        ))}
                    </List>
                )
            }
        </div>
    );
}