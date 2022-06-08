import React, {useEffect} from "react";
import Item, {LoadingItem} from "./Item";
import Paginator, {LoadingPaginator} from "./Paginator";

export default function List(props) {
    const {
        paginationData,
        changePage,
        children,
        loading = false
    } = props;

    return (
        <div>
            <Paginator
                paginationData={paginationData}
                onChange={changePage}
                loading={loading}
            />

            {children}

            <Paginator
                paginationData={paginationData}
                onChange={changePage}
                loading={loading}
            />
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