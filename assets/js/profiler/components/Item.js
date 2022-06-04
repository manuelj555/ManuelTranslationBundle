import React, {useContext} from "react";
import GlobalsContext from "../context/GlobalsContext";

const Item = ({item}) => {
    const {locales} = useContext(GlobalsContext);
    const parameters = Array.from(Object.keys(item.parameters));

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
                {locales.map(locale => (
                    <div key={locale}>
                        <span>{locale}</span>
                        <textarea defaultValue={item.code}></textarea>
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