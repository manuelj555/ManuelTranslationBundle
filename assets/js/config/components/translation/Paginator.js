import React from "react";
import {Pagination, Placeholder} from "react-bootstrap";
import {calculatePagesCount, itemsPerPage} from "../../context/GlobalsContext";

const Paginator = React.memo(({paginationData, onChange, loading}) => {
    const {page: currentPage, totalCount} = paginationData;
    const pagesCount = calculatePagesCount(totalCount);
    const itemsCount = itemsPerPage > totalCount ? totalCount : itemsPerPage;

    const prev = currentPage > 1 ? currentPage - 1 : null;
    const next = currentPage < pagesCount ? currentPage + 1 : null;

    const goToPage = onChange;

    return (
        <div className="d-flex my-2 align-items-center">
            <Pagination className="mb-0" size="sm">
                <Pagination.Prev disabled={!prev} onClick={() => goToPage(prev)}>Prev</Pagination.Prev>
                <Pagination.Item active>{currentPage}</Pagination.Item>
                <Pagination.Next disabled={!next} onClick={() => goToPage(next)}>Next</Pagination.Next>
            </Pagination>
            {loading && <span className="ms-2">Loading...</span>}
            <div className="ms-auto">
                <b>Items:</b> {itemsCount} <b>of</b> {totalCount}
            </div>
        </div>
    );
});

export function LoadingPaginator() {
    return (
        <div className="d-flex gap-2 my-2">
            <Placeholder xs={2}/>
            <Placeholder className="ms-auto py-3" xs={2}/>
        </div>
    );
}

export default Paginator;