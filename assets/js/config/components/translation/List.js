import React, {useEffect} from "react";
import Item, {LoadingItem} from "./Item";
import {LoadingPaginator} from "./Paginator";

export default function List(props) {
    const {
        translations,
        pagination,
        saveItem,
        removeEmptyItem,
        addEmptyItem,
    } = props;

    useEffect(() => {
        const addBtn = document.getElementById('add-translation');

        addBtn.addEventListener('click', handleAddClick);

        return () => addBtn.removeEventListener('click', handleAddClick);
    }, []);

    const handleAddClick = (e) => {
        e?.preventDefault();
        addEmptyItem();
    };


    return (
        <div>
            {pagination}

            <div>
                {translations.map(translation => (
                    <Item
                        key={translation.uuid}
                        translation={translation}
                        saveItem={saveItem}
                        removeEmptyItem={removeEmptyItem}
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