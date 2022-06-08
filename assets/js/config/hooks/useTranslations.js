import {useCallback, useMemo, useState} from "react";
import useTranslationsQuery from "./useTranslationsQuery";

const useTranslations = () => {
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
        const addEmptyItem = () => addEmpty();
        const removeEmptyItem = (item) => removeEmpty(item);

        return {addEmptyItem, removeEmptyItem};
    }, [addEmpty, removeEmpty, persistItem]);

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