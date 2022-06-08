import {useCallback, useContext, useMemo, useState} from "react";
import GlobalsContext from "../context/GlobalsContext";
import useTranslationsQuery from "./useTranslationsQuery";

const useTranslations = () => {
    const {addDomain} = useContext(GlobalsContext);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
    }));
    const [page, setPage] = useState(1);
    const translationsQuery = useTranslationsQuery(filters, page)
    const {totalCount, addEmpty, removeEmpty, saveItem: persistItem} = translationsQuery

    const setConfig = useCallback((config) => {
        if (config?.filters) {
            setFilters(f => ({...f, ...config.filters}));
            setPage(1);
        } else if (config?.page) {
            setPage(config?.page);
        }
    }, [])

    const translationActions = useMemo(() => {
        const saveItem = (item) => persistItem(item).then(({data}) => {
            data?.domain && addDomain(data.domain)
        })
        const addEmptyItem = () => addEmpty();
        const removeEmptyItem = (item) => removeEmpty(item);

        return {saveItem, addEmptyItem, removeEmptyItem};
    }, [addDomain, addEmpty, removeEmpty, persistItem]);

    return {
        isLoading: translationsQuery.isLoading,
        isFetching: translationsQuery.isFetching,
        translations: translationsQuery.translations,
        config: {filters, pagination: {page, totalCount}},
        setConfig,
        translationActions,
    };
}

export default useTranslations;