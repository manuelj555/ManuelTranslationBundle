import React, {useEffect} from "react";
import Item, {LoadingItem} from "./Item";
import Paginator, {LoadingPaginator} from "./Paginator";

export default function List(props) {
    const {
        paginationData,
        changePage,
        addEmptyItem,
        children,
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

    const paginationContent = (
        <Paginator
            paginationData={paginationData}
            onChange={changePage}
        />
    );

    return (
        <div>
            {paginationContent}

            {children}

            {paginationContent}
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