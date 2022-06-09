import {useCallback, useContext, useEffect} from "react";
import GlobalsContext, {itemsPerPage} from "../context/GlobalsContext";
import axios from "axios";
import {v4 as uuid} from "uuid";
import {useQuery, useQueryClient} from "react-query";

const createNewItem = () => ({
    id: null,
    uuid: uuid(),
    code: '',
    domain: 'messages',
    active: true,
    values: {},
});

const getTranslations = (apiUrl, page, itemsPerPage, filters) => {
    return axios.get(apiUrl, {
        params: {
            search: filters?.search || '',
            domains: (filters?.domains || []).filter(d => d.length > 0),
            page: page,
            perPage: itemsPerPage,
        }
    }).then(({data, headers}) => {
        return {
            items: data.map(item => ({...item, uuid: item.id})),
            totalCount: headers['x-count'],
        };
    })
}

const useTranslationsQuery = (filters, page) => {
    const {paths: {api: apiUrl}} = useContext(GlobalsContext);
    const queryClient = useQueryClient()

    const queryKey = ["translations", "list", apiUrl, page, itemsPerPage, filters];

    const translationsQuery = useQuery(
        queryKey,
        () => getTranslations(apiUrl, page, itemsPerPage, filters),
        {
            keepPreviousData: true,
        })

    const {isLoading, isFetching, data: {items: translations = [], totalCount = 0} = {}} = translationsQuery

    useEffect(() => {
        queryClient.removeQueries(["translations", "list"], {active: false})
    }, [filters])

    const addEmpty = useCallback(() => {
        queryClient.setQueryData(queryKey, ({items, totalCount}) => {
            return {
                items: [createNewItem(), ...items],
                totalCount,
            }
        })
    }, [queryClient, queryKey])

    const removeEmpty = useCallback((item) => {
        queryClient.setQueryData(queryKey, ({items, totalCount}) => {
            return {
                items: items.filter(({uuid}) => uuid !== item.uuid),
                totalCount,
            }
        })
    }, [queryClient, queryKey])


    return {
        isLoading,
        isFetching,
        translations,
        totalCount,
        addEmpty,
        removeEmpty
    };
}

export default useTranslationsQuery;