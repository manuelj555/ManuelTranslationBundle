import React from "react";
import Item from "./Item";

const App = ({items}) => {

    console.log(items);

    return (
        <div className="missing-translations-creator">
            {items.map(item => (
                <Item key={item.code} item={item}/>
            ))}
        </div>
    )
}

export default App;