import React, {createContext, useContext, useEffect, useState} from "react";
import GlobalsContext from "./GlobalsContext";
import axios from "axios";

const TranslationsContext = createContext({
    translations: [],
    applyFilters: (newFilters) => null,
});

const TranslationsProvider = ({children}) => {
    const {paths} = useContext(GlobalsContext);
    const [translations, setTranslations] = useState([]);
    const [domains, setDomains] = useState([]);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
        showInactive: false,
    }));

    useEffect(() => {
        axios.get(paths.list)
            .then(({data}) => data)
            .then(data => {
                setTranslations(data);
            })
    }, []);

    useEffect(() => {
        console.log('Aplicando filtros');
    }, [filters]);

    const applyFilters = (newFilters) => {
        setFilters({...filters, ...newFilters});
    };

    return (
        <TranslationsContext.Provider value={{
            translations,
            applyFilters,
        }}>
            {children}
        </TranslationsContext.Provider>
    );
};

export {TranslationsProvider};
export default TranslationsContext;