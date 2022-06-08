import {useMutation, useQueryClient} from "react-query";
import axios from "axios";
import {useContext} from "react";
import GlobalsContext from "../context/GlobalsContext";

const persistTranslation = (apiUrl, item) => {
    const method = item.id ? axios.put : axios.post
    const url = item.id ? apiUrl + "/" + item.id : apiUrl

    return method(url, {
        ...item,
        lastChanged: 'local',
    })
}

const useMutateItem = () => {
    const {paths: {api}, addDomain} = useContext(GlobalsContext)
    const queryClient = useQueryClient()

    const itemMutation = useMutation((item) => persistTranslation(api, item), {
        onSuccess({data}) {
            const queryKeyFilter = ["translations", "list"]

            data?.domain && addDomain(data.domain)

            queryClient.setQueriesData(queryKeyFilter, ({items, totalCount}) => {
                const newItems = [...items]
                const index = newItems.findIndex(item => item.id === data.id)

                if (-1 !== index) {
                    // actualizamos la data al momento
                    newItems[index] = {...newItems[index], ...data}
                }

                return {items: newItems, totalCount}
            })
            queryClient.invalidateQueries(queryKeyFilter)
        }
    })

    return {
        save: itemMutation.mutateAsync,
        isLoading: itemMutation.isLoading,
    }
}

export default useMutateItem
