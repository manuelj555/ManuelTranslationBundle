import React from "react";
import {Pagination} from "react-bootstrap";
import {calculatePagesCount, itemsPerPage} from "../../context/GlobalsContext";

export default function Paginator({currentPage, totalCount, onChange}) {
    const pagesCount = calculatePagesCount(totalCount);
    const itemsCount = itemsPerPage > totalCount ? totalCount : itemsPerPage;

    const prev = currentPage > 1 ? currentPage - 1 : null;
    const next = currentPage + 1 < pagesCount ? currentPage + 1 : null;

    const goToPage = (page) => onChange(page);

    return (
        <div className="d-flex my-2 align-items-center">
            <Pagination className="mb-0">
                <Pagination.Prev disabled={!prev} onClick={() => goToPage(prev)}>Prev</Pagination.Prev>
                <Pagination.Item active>{currentPage}</Pagination.Item>
                <Pagination.Next disabled={!next} onClick={() => goToPage(next)}>Next</Pagination.Next>
            </Pagination>
            <div className="ms-auto">
                <b>Items:</b> {itemsCount} <b>of</b> {totalCount}
            </div>
        </div>
    );
}