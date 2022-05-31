import React, {createContext, useContext, useEffect, useState} from "react";
import GlobalsContext from "./GlobalsContext";
import axios from "axios";

const TranslationsContext = createContext({
    translations: [],
});

const TranslationsProvider = ({children}) => {
    const {paths} = useContext(GlobalsContext);
    const [translations, setTranslations] = useState([]);

    useEffect(() => {
        axios.get(paths.list)
            .then(({data}) => data)
            .then(data => {
                setTranslations(data);
            })
    }, []);

    return (
        <TranslationsContext.Provider value={{
            translations,
        }}>
            {children}
        </TranslationsContext.Provider>
    );
};

export {TranslationsProvider};
export default TranslationsContext;