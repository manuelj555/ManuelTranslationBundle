import React, {useContext, useEffect} from "react";
import Item, {LoadingItem} from "./Item";
import TranslationsContext from "../../context/TranslationsContext";
import Paginator, {LoadingPaginator} from "./Paginator";

export default function List() {
    const {
        translations,
        currentPage,
        totalCount,
        changePage,
        addEmptyItem,
        saveItem,
        removeEmptyItem,
    } = useContext(TranslationsContext);

    useEffect(() => {
        const addBtn = document.getElementById('add-translation');

        addBtn.addEventListener('click', handleAddClick);

        return () => addBtn.removeEventListener('click', handleAddClick);
    }, []);

    const handleAddClick = (e) => {
        e?.preventDefault();
        addEmptyItem();
    };

    const pagination = (
        <Paginator
            currentPage={currentPage}
            totalCount={totalCount}
            onChange={changePage}
        />
    );

    return (
        <div>
            {pagination}

            <div>
                {translations.map(translation => (
                    <Item
                        key={translation.uuid}
                        translation={translation}
                        saveItemHandler={saveItem}
                        removeEmptyItemHandler={removeEmptyItem}
                    />
                ))}
            </div>

            {pagination}

        </div>
    );
}

const LoadingList = () => {
    return (
        <div>
            <LoadingPaginator/>

            <LoadingItem/>
            <LoadingItem/>
            <LoadingItem/>

            <LoadingPaginator/>
        </div>
    );
};

export {LoadingList};