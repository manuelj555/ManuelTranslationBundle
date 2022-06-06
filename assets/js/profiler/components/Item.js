import React, {startTransition, useState} from "react";

const STATUS_EDITING = 'editing'
const STATUS_PERSISTING = 'persisting'
const STATUS_PERSISTED = 'persisted'
const STATUS_ERROR = 'error'

const persistButtonLabel = (status) => {
    switch (status) {
        case STATUS_PERSISTING:
            return 'Persisting...!!!'
        case STATUS_PERSISTED:
            return 'DONE'
        default:
            return 'Create';
    }
}

const Item = ({item, onChange, onSubmit}) => {
    const [status, setStatus] = useState(STATUS_EDITING);
    const [message, setMessage] = useState('');
    const parameters = Array.from(Object.keys(item.parameters));
    const {values} = item
    const valuesMap = Object.entries(values);

    const handleValueChange = (locale, event) => {
        const newValues = {...values, [locale]: event.target.value};

        onChange(item.id, {values: newValues});
    }

    const handleCreateClick = () => {
        startTransition(() => {
            setMessage('')
            setStatus(STATUS_PERSISTING)
        });
        onSubmit(item.id).then(() => {
            setMessage('')
            setStatus(STATUS_PERSISTED)
        }).catch((message) => {
            setMessage(message)
            setStatus(STATUS_ERROR)
        });
    }

    return (
        <div className={
            `translation-item-creator ${status}`
        }>
            {message?.length > 0
                ? (
                    <div className={`message ${status}`}>{message}</div>
                ) : null
            }

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
                            disabled={STATUS_PERSISTING === status}
                            value={value}
                            onChange={(e) => handleValueChange(locale, e)}
                        />
                    </div>
                ))}
            </div>
            <div className="item-actions">
                <div className="btn-container">
                    <button
                        onClick={handleCreateClick}
                        disabled={STATUS_PERSISTING === status}
                    >
                        {persistButtonLabel(status)}
                    </button>
                </div>
            </div>
        </div>
    )
}

export default Item;