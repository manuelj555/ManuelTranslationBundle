import React, {createContext, useState} from "react";

const LoadingContext = createContext({
    appLoading: true,
    setAppLoading: (loading) => null,
});

const LoadingProvider = ({children}) => {
    const [appLoading, setAppLoading] = useState(true);

    return (
        <LoadingContext.Provider value={{
            appLoading,
            setAppLoading,
            // setAppLoading: () => null,
        }}>
            {children}
        </LoadingContext.Provider>
    );
}

export {LoadingProvider};
export default LoadingContext;