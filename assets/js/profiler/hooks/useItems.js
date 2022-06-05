import {useContext, useEffect, useState} from "react";
import axios from "axios";
import GlobalsContext from "../context/GlobalsContext";

const equalItems = (a, b) => (a.code === b.code && a.domain === b.domain);

export default function useItems(defaultItems) {
    const [items, setItems] = useState(false);
    const {paths: {getMissing}, locales} = useContext(GlobalsContext);

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
                        values: locales.reduce((localesObj, locale) => ({
                            ...localesObj,
                            [locale]: item.code,
                        }), {}),
                    }
                }))
            });
    }, [defaultItems]);

    const updateItem = (newItemData) => {
        const newItems = [...items];
        const indexToUpdate = newItems.findIndex(item => equalItems(item, newItemData));

        if (0 > indexToUpdate) {
            return;
        }

        newItems[indexToUpdate] = {...newItemData};
        setItems(newItems);
    }

    return {
        items,
        updateItem,
    }
}