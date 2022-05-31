import React, {createContext} from "react";

const GlobalsContext = createContext({
    paths: {},
    booleanLabel: (value) => null,
});

const GlobalsResolver = ({children, paths}) => {

    const booleanLabel = (value) => {
        return value ? 'Yes' : 'No';
    };

    return (
        <GlobalsContext.Provider value={{
            paths,
            booleanLabel,
        }}>
            {children}
        </GlobalsContext.Provider>
    );
}

export {GlobalsResolver};
export default GlobalsContext;