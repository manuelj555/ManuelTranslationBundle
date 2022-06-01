import {useEffect, useState} from "react";

const useTranslationValidator = (item) => {
    const [valid, setValid] = useState();
    const [errors, setErrors] = useState();

    useEffect(() => {
        const errors = {};

        if (!item.code || item.code.trim().length === 0) {
            errors['code'] = errors['code'] || [];
            errors['code'].push('This field is required');
        }

        if (item.code && item.code.trim().length < 3) {
            errors['code'] = errors['code'] || [];
            errors['code'].push('This field must contains 3 characters or more');
        }

        if (item.code && item.code.trim().match(/\s/)) {
            errors['code'] = errors['code'] || [];
            errors['code'].push('This field cannot have blank spaces');
        }

        Object.entries(item?.values || {}).map(([locale, value]) => {
            if (value.trim().length === 0) {
                const index = locale.toUpperCase() + ' Value';
                errors[index] = errors[index] || [];
                errors[index].push('This field is required');
            }
        });

        setErrors(errors);
        setValid(Object.keys(errors).length === 0);
    }, [item]);

    return {valid, errors};
}

export default useTranslationValidator;