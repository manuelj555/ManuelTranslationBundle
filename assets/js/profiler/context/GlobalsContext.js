import React, {createContext} from "react";

const GlobalsContext = createContext({
    locales: [],
});

const GlobalsProvider = ({children, locales}) => {
    return (
        <GlobalsContext.Provider value={{
            locales,
        }}>
            {children}
        </GlobalsContext.Provider>
    );
}

export {GlobalsProvider};
export default GlobalsContext;