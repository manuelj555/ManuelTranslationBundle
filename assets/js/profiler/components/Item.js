import React, {useState} from "react";

const Item = ({item, onChange, onSubmit}) => {
    const [isPersisting, setPersisting] = useState(false);
    const [isPersisted, setPersisted] = useState(false);
    const parameters = Array.from(Object.keys(item.parameters));
    const {values} = item
    const valuesMap = Object.entries(values);

    const handleValueChange = (locale, event) => {
        const newValues = {...values, [locale]: event.target.value};

        onChange(item.id, {values: newValues});
    }

    const handleCreateClick = () => {
        setPersisting(true);
        onSubmit(item.id).then(() => {
            setPersisting(false)
            setPersisted(true);
        });
    }

    return (
        <div className={
            `translation-item-creator ${isPersisting ? 'persisting' : ''} ${isPersisted ? 'persisted' : ''}`
        }>
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
                        <textarea
                            disabled={isPersisting}
                            value={value}
                            onChange={(e) => handleValueChange(locale, e)}
                        />
                    </div>
                ))}
            </div>
            <div className="item-actions">
                <button
                    onClick={handleCreateClick}
                    disabled={isPersisting}
                >{isPersisting ? 'Creating...!' : (isPersisted ? 'Created...!' : 'Create')}
                </button>
            </div>
        </div>
    )
}

export default Item;