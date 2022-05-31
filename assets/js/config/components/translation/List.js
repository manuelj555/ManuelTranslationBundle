import React, {useContext} from "react";
import Item from "./Item";
import TranslationsContext from "../../context/TranslationsContext";
import Paginator from "./Paginator";

export default function List() {
    const {translations, currentPage, totalCount, changePage} = useContext(TranslationsContext);

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