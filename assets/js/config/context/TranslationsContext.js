import React, {createContext, useContext, useEffect, useState} from "react";
import GlobalsContext, {itemsPerPage} from "./GlobalsContext";
import axios from "axios";
import LoadingContext from "./LoadingContext";
import {v4 as uuid} from 'uuid';

const TranslationsContext = createContext({
    translations: [],
    currentPage: 1,
    totalCount: 0,
    changePage: (page) => null,
    applyFilters: (newFilters) => null,
    saveItem: (item) => null,
    addEmptyItem: () => null,
    removeEmptyItem: (item) => null,
});

const TranslationsProvider = ({children}) => {
    const {paths: {api: apiUrl}} = useContext(GlobalsContext);
    const {setAppLoading} = useContext(LoadingContext);
    const [translations, setTranslations] = useState([]);
    const [page, setPage] = useState(1);
    const [totalCount, setTotalCount] = useState(0);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
        showInactive: false,
    }));

    const loadTranslations = () => {
        setAppLoading(true);
        axios.get(apiUrl, {
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
                setTranslations(data.map(item => ({...item, uuid: uuid()})));
                setAppLoading(false);
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

    const saveItem = (item) => {
        let ajaxRequest = null;
        const itemUuid = item.uuid;

        if (item.id) {
            ajaxRequest = axios.put(apiUrl + '/' + item.id, item);
        } else {
            ajaxRequest = axios.post(apiUrl, item);
        }

        return ajaxRequest
            .then(({data}) => data)
            .then((item) => {
                setTranslations(translations => {
                    const newTranslations = [...translations];
                    const itemIndex = newTranslations.findIndex(i => i.uuid === itemUuid);
                    item = {...item, uuid: itemUuid};

                    if (0 <= itemIndex) {
                        newTranslations[itemIndex] = item;
                    } else {
                        //item nuevo
                        newTranslations.unshift(item);
                    }

                    return newTranslations;
                })
            });
    };

    const addEmptyItem = () => {
        setTranslations(translations => {
            const newTranslations = [...translations];
            newTranslations.unshift({
                id: null,
                uuid: uuid(),
                code: '',
                domain: 'messages',
                active: true,
                values: {},
            });

            return newTranslations;
        })
    };

    const removeEmptyItem = (item) => {
        setTranslations(translations => translations.filter(({uuid}) => uuid !== item.uuid));
    };

    return (
        <TranslationsContext.Provider value={{
            translations,
            currentPage: page,
            totalCount,
            changePage,
            applyFilters,
            saveItem,
            addEmptyItem,
            removeEmptyItem,
        }}>
            {children}
        </TranslationsContext.Provider>
    );
};

export {TranslationsProvider};
export default TranslationsContext;