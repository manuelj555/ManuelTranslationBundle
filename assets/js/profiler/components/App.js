import React, {useContext} from "react";
import Item from "./Item";
import GlobalsContext from "../context/GlobalsContext";

const App = ({items}) => {
    const {paths} = useContext(GlobalsContext);

    console.log(items);

    return (
        <div className="missing-translations-creator">
            <header>
                <h2>Create Missing Translations</h2>
                <a href={paths.list}>Translations List</a>
            </header>

            {items.map(item => (
                <Item key={item.code} item={item}/>
            ))}
        </div>
    )
}

export default App;