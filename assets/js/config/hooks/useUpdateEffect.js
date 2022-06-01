import {useEffect, useRef} from "react";

const useUpdateEffect = (callback, dependencies = null) => {
    const firstCallRef = useRef(true);

    useEffect(() => {
        if (firstCallRef.current) {
            // se usa timeout para evitar el segundo llamado al tener el react.strictMode activo.
            setTimeout(() => firstCallRef.current = false, 50);
        } else {
            callback();
        }
    }, dependencies);
};

export default useUpdateEffect;