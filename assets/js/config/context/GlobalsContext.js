import React, {createContext} from "react";

const GlobalsContext = createContext({
    paths: {},
    domains: [],
    booleanLabel: (value) => null,
});

const GlobalsResolver = ({children, paths, domains}) => {

    const booleanLabel = (value) => {
        return value ? 'Yes' : 'No';
    };

    const domainsAsArray = Object.entries(domains).map(([key, value]) => value);

    return (
        <GlobalsContext.Provider value={{
            paths,
            domains: domainsAsArray,
            booleanLabel,
        }}>
            {children}
        </GlobalsContext.Provider>
    );
}

export {GlobalsResolver};
export default GlobalsContext;