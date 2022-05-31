import React, {useContext} from "react";
import Item, {LoadingItem} from "./Item";
import TranslationsContext from "../../context/TranslationsContext";
import Paginator, {LoadingPaginator} from "./Paginator";

export default function List() {
    const {
        translations,
        currentPage,
        totalCount,
        changePage,
    } = useContext(TranslationsContext);

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
                    <Item key={translation.id} translation={translation}/>
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