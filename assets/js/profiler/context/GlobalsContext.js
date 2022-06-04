import React, {createContext} from "react";

const GlobalsContext = createContext({
    locales: [],
    paths: {
        list: '',
    },
});

const GlobalsProvider = ({children, locales, paths}) => {
    return (
        <GlobalsContext.Provider value={{
            locales,
            paths,
        }}>
            {children}
        </GlobalsContext.Provider>
    );
}

export {GlobalsProvider};
export default GlobalsContext;