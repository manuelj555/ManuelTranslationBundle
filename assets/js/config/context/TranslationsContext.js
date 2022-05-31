import React, {createContext, useContext, useEffect, useState} from "react";
import GlobalsContext, {itemsPerPage} from "./GlobalsContext";
import axios from "axios";

const TranslationsContext = createContext({
    translations: [],
    currentPage: 1,
    totalCount: 0,
    changePage: (page) => null,
    applyFilters: (newFilters) => null,
});

const TranslationsProvider = ({children}) => {
    const {paths} = useContext(GlobalsContext);
    const [translations, setTranslations] = useState([]);
    const [page, setPage] = useState(1);
    const [totalCount, setTotalCount] = useState(0);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
        showInactive: false,
    }));

    const loadTranslations = () => {
        axios.get(paths.list, {
            params: {
                search: filters.search,
                domains: filters.domains.filter(d => d.length > 0),
                inactive: filters.showInactive,
                page,
                perPage: itemsPerPage,
            }
        })
            .then(({data, headers}) => {
                const totalCount = headers['x-count'];
                setTotalCount(totalCount);

                return data;
            })
            .then(data => {
                setTranslations(data);
            })
    }

    useEffect(() => {
        loadTranslations();
    }, [filters, page]);

    const applyFilters = (newFilters) => {
        setPage(1);
        setFilters({...filters, ...newFilters});
    };

    const changePage = (page) => {
        setPage(page);
    };

    return (
        <TranslationsContext.Provider value={{
            translations,
            currentPage: page,
            totalCount,
            changePage,
            applyFilters,
        }}>
            {children}
        </TranslationsContext.Provider>
    );
};

export {TranslationsProvider};
export default TranslationsContext;