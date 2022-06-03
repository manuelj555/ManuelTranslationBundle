import React, {createContext, useCallback, useState} from "react";

const GlobalsContext = createContext({
    paths: {},
    domains: [],
    locales: [],
    booleanLabel: (value) => null,
    addDomain: (domain) => null,
});

const itemsPerPage = 50;

const calculatePagesCount = (totalCount) => {
    return Math.floor(totalCount / itemsPerPage) + 1;
}

const GlobalsProvider = ({children, paths, domains, locales}) => {
    const [appDomains, setDomains] = useState(() => {
        return Object.entries(domains).map(([key, value]) => value);
    });

    const booleanLabel = (value) => {
        return value ? 'Yes' : 'No';
    };

    const addDomain = useCallback((domain) => {
        setDomains(domains => {
            if (domains.includes(domain)) {
                return domains;
            }

            return [...domains, domain];
        });
    }, []);

    return (
        <GlobalsContext.Provider value={{
            paths,
            domains: appDomains,
            addDomain,
            locales,
            booleanLabel,
        }}>
            {children}
        </GlobalsContext.Provider>
    );
}

export {GlobalsProvider, calculatePagesCount, itemsPerPage};
export default GlobalsContext;