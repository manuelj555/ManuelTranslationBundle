import {useMutation, useQueryClient} from "react-query";
import axios from "axios";

const persistTranslation = (apiUrl, item) => {
    const method = item.id ? axios.put : axios.post
    const url = item.id ? apiUrl + "/" + item.id : apiUrl

    return method(url, {
        ...item,
        lastChanged: 'local',
    })
}

const useMutateItem = (apiUrl) => {
    const queryClient = useQueryClient()

    const itemMutation = useMutation((item) => persistTranslation(apiUrl, item), {
        onSuccess({data}) {
            // queryClient.setQueryData(queryKey, ({items, totalCount}) => {
            //     const newItems = [...items]
            //     const index = newItems.findIndex(item => item.id === data.id)
            //
            //     if (-1 !== index) {
            //         // actualizamos la data al momento
            //         newItems[index] = {...newItems[index], ...data}
            //     }
            //
            //     return {items: newItems, totalCount}
            // })
            // queryClient.invalidateQueries(["translations", "list"])
        }
    })

    return {
        save: itemMutation.mutateAsync,
        isLoading: itemMutation.isLoading,
    }
}

export default useMutateItem
