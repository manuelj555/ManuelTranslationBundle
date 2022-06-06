import {useContext, useEffect, useState} from "react";
import axios from "axios";
import GlobalsContext from "../context/GlobalsContext";
import {v4 as uuid} from "uuid";

const equalItems = (a, b) => (a.code === b.code && a.domain === b.domain);

export default function useItems(defaultItems) {
    const [items, setItems] = useState(false);
    const {paths: {getMissing, create: createPath}, locales} = useContext(GlobalsContext);

    useEffect(() => {
        const search = defaultItems.map(item => ({code: item.code, domain: item.domain}));

        axios.post(getMissing, search)
            .then(({data}) => data)
            .then(missing => {
                const existsInMissing = (item) => {
                    return missing?.some(missingItem => equalItems(missingItem, item))
                }

                setItems(defaultItems.filter(existsInMissing).map(item => {
                    return {
                        ...item,
                        id: uuid(),
                        values: locales.reduce((localesObj, locale) => ({
                            ...localesObj,
                            [locale]: item.code,
                        }), {}),
                    }
                }))
            });
    }, [defaultItems]);

    const updateItem = (id, newItemData) => {
        const newItems = [...items];
        const indexToUpdate = newItems.findIndex(item => item.id === id);

        if (0 > indexToUpdate) {
            return;
        }

        const item = newItems[indexToUpdate];

        newItems[indexToUpdate] = {...item, ...newItemData};
        setItems(newItems);
    }

    const persistItem = (id) => {
        const itemToPersist = items.find(item => item.id === id);

        if (!itemToPersist) {
            return;
        }

        const {code, domain, values} = itemToPersist

        return new Promise((resolve, reject) => {
            axios.post(createPath, {code, domain, values}).then(() => {
                resolve();

                setTimeout(() => {
                    setItems(oldItems => (oldItems.filter(item => item.id !== id)))
                }, 1100)
            }).catch(({message, response}) => {
                let error = message

                if (response.status === 400) {
                    error = JSON.stringify(response.data  || message, null, 1);
                }

                // if (response.status === 0) {
                //     return Promise.reject(message);
                // } else if (response.status >= 500) {
                //     console.log(response)
                //     return Promise.reject(message);
                // } else {
                //     console.log(response)
                //     error = message;
                // }

                reject(error);
            })
        })
    }

    return {
        items,
        updateItem,
        persistItem,
    }
}