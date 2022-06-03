import {startTransition, useCallback, useContext, useEffect, useMemo, useState} from "react";
import GlobalsContext from "../context/GlobalsContext";
import axios from "axios";
import {v4 as uuid} from "uuid";

const itemsPerPage = 50;

const createNewItem = () => ({
    id: null,
    uuid: uuid(),
    code: '',
    domain: 'messages',
    active: true,
    values: {},
});

const useTranslations = () => {
    const {paths: {api: apiUrl}, addDomain} = useContext(GlobalsContext);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
    }));
    const [pagination, setPagination] = useState({
        page: 1,
        totalCount: 0,
    });
    const [translations, setTranslations] = useState([]);
    const [isLoading, setLoading] = useState(true);

    const loadTranslations = () => {
        setLoading(true);
        axios.get(apiUrl, {
            params: {
                search: filters?.search || '',
                domains: (filters?.domains || []).filter(d => d.length > 0),
                page: pagination.page,
                perPage: itemsPerPage,
            }
        })
            .then(({data, headers}) => {
                return [data, headers['x-count']];
            })
            .then(([data, totalCount]) => {
                setTranslations(data.map(item => ({...item, uuid: uuid()})));
                setLoading(false);
                startTransition(() => {
                    setPagination(pagination => ({...pagination, totalCount}));
                })
            })
    };

    useEffect(() => {
        loadTranslations();
    }, [filters, pagination.page]);

    const setConfig = useCallback((config) => {
        if (config?.filters) {
            setFilters(f => ({...f, ...config.filters}));
            setPagination(p => ({...p, page: 1}));
        } else if (config?.page) {
            setPagination(p => ({...p, page: config.page}));
        }
    }, [])

    const translationActions = useMemo(() => {
        const saveItem = (item) => {
            const ajaxMethod = item.id ? axios.put : axios.post;
            const ajaxUrl = apiUrl + (item.id ? `/${item.id}` : '');
            const itemUuid = item.uuid;

            const onSuccess = (item) => {
                setTranslations(translations => {
                    const newTranslations = [...translations];
                    const itemIndex = newTranslations.findIndex(i => i.uuid === itemUuid);
                    item = {...item, uuid: itemUuid};

                    if (0 <= itemIndex) {
                        newTranslations[itemIndex] = item;
                    } else {
                        //item nuevo
                        newTranslations.unshift(item);
                        startTransition(() => {
                            setPagination(pagination => ({
                                ...pagination,
                                totalCount: pagination.totalCount + 1,
                            }));
                            addDomain(item.domain);
                        })
                    }

                    return newTranslations;
                })
            };

            return ajaxMethod(ajaxUrl, item).then(({data}) => data).then(onSuccess);
        };

        const addEmptyItem = () => {
            setTranslations(translations => [createNewItem(), ...translations])
        };

        const removeEmptyItem = (item) => {
            setTranslations(translations =>
                translations.filter(({uuid}) => uuid !== item.uuid)
            );
        };

        return {saveItem, addEmptyItem, removeEmptyItem};
    }, [apiUrl, addDomain]);

    return {
        isLoading,
        translations,
        config: {filters, pagination},
        setConfig,
        translationActions,
    };
}

export {
    itemsPerPage,
}
export default useTranslations;