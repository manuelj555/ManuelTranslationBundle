import React from "react";

const Item = ({item, onChange}) => {
    const parameters = Array.from(Object.keys(item.parameters));
    const {values} = item
    const valuesMap = Object.entries(values);

    const handleValueChange = (locale, event) => {
        const newValues = {...values, [locale]: event.target.value};

        onChange({
            ...item,
            values: newValues,
        });
    }

    return (
        <div className="translation-item-creator">
            <div className="item-data">
                <div>Code<span>{item.code}</span></div>
                <div>Domain<span>{item.domain}</span></div>
                {parameters.length > 0
                    ? (
                        <div>
                            Parameters
                            <span className="item-parameters">
                                {parameters.map((p, key) => (<span key={key}>{p}</span>))}
                            </span>
                        </div>
                    ) : null
                }
            </div>
            <div className="item-values">
                {valuesMap.map(([locale, value]) => (
                    <div key={locale}>
                        <span>{locale}</span>
                        <textarea value={value} onChange={(e) => handleValueChange(locale, e)}/>
                    </div>
                ))}
            </div>
            <div className="item-actions">
                <button>Create</button>
            </div>
        </div>
    )
}

export default Item;