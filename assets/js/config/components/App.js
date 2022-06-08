import React, {useEffect} from "react";
import Filter from "./translation/Filter";
import List, {LoadingList} from "./translation/List";
import Item from "./translation/Item";
import useTranslations from "../hooks/useTranslations";

export default function App() {
    const {
        translations,
        isLoading,
        isFetching,
        config,
        setConfig,
        translationActions,
    } = useTranslations()


    useEffect(() => {
        const addBtn = document.getElementById('add-translation')

        const handleAddClick = (e) => {
            e?.preventDefault()
            translationActions.addEmptyItem()
        }

        addBtn.addEventListener('click', handleAddClick)

        return () => addBtn.removeEventListener('click', handleAddClick)
    }, [translationActions.addEmptyItem])

    const applyFilters = (filters) => setConfig({filters})
    const changePage = (page) => setConfig({page})

    return (
        <div>
            <Filter onSubmit={applyFilters}/>
            {isLoading
                ? <LoadingList/>

                : (
                    <List
                        paginationData={config.pagination}
                        changePage={changePage}
                        loading={isFetching}
                    >
                        {translations.map(translation => (
                            <Item
                                key={translation.id}
                                translation={translation}
                                removeEmptyItem={translationActions.removeEmptyItem}
                            />
                        ))}
                    </List>
                )
            }
        </div>
    );
}