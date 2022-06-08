import {useCallback, useContext} from "react";
import GlobalsContext, {itemsPerPage} from "../context/GlobalsContext";
import axios from "axios";
import {v4 as uuid} from "uuid";
import {useMutation, useQuery, useQueryClient} from "react-query";

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
            items: data.map(item => ({...item, uuid: uuid()})),
            totalCount: headers['x-count'],
        };
    })
}

const persistTranslation = (apiUrl, item) => {
    const method = item.id ? axios.put : axios.post
    const url = item.id ? apiUrl + "/" + item.id : apiUrl

    return method(url, {
        ...item,
        lastChanged: 'local',
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
            initialData: () => ({items: [], totalCount: 0}),
            keepPreviousData: true,
        })

    const {isLoading, isFetching, data: {items: translations, totalCount}} = translationsQuery

    const saveItemMutation = useMutation((item) => persistTranslation(apiUrl, item), {
        onSuccess({data}) {
            queryClient.setQueryData(queryKey, ({items, totalCount}) => {
                const newItems = [...items]
                const index = newItems.findIndex(item => item.id === data.id)

                if (-1 !== index) {
                    // actualizamos la data al momento
                    newItems[index] = {...newItems[index], ...data}
                }

                return {items: newItems, totalCount}
            })
            queryClient.invalidateQueries(["translations", "list"])
        }
    })

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

    const saveItem = useCallback((item) => {
        return saveItemMutation.mutateAsync(item)
    }, [saveItemMutation])


    return {
        isLoading,
        isFetching,
        translations,
        totalCount,
        addEmpty,
        removeEmpty,
        saveItem,
    };
}

export default useTranslationsQuery;