import React, {useState} from "react";
import ConflictItem from "./ConflictItem";
import {Button} from "react-bootstrap";

const initialSelectedItemsState = {
    file: [],
    database: [],
};

const App = ({defaultItems}) => {
    const [items, setItems] = useState(() => defaultItems);
    const [selectedItems, setSelectedItems] = useState(initialSelectedItemsState);

    const changeSelectAll = (type) => {
        if (type === 'none') {
            setSelectedItems({...initialSelectedItemsState});
        } else {
            setSelectedItems({
                ...initialSelectedItemsState,
                [type]: items.map(item => item.database.id)
            });
        }

    }

    const handleSelectAllFileClick = () => changeSelectAll("file");
    const handleSelectAllDatabaseClick = () => changeSelectAll("database");
    const handleClearAllClick = () => changeSelectAll("none");

    const handleItemSelectionChange = (item, selectionType) => {
        if (!["file", "database", "none"].includes(selectionType)) {
            return;
        }

        setSelectedItems(oldSelected => {
            const currentItemId = item.database.id;
            const newItems = {
                file: oldSelected.file.filter(id => id !== currentItemId),
                database: oldSelected.database.filter(id => id !== currentItemId),
            }

            if ('none' === selectionType) {
                return newItems;
            }

            newItems[selectionType].push(currentItemId);

            return newItems;
        });
    }

    const itemSelectionType = (item) => {
        const itemId = item.database.id;

        return selectedItems.file.includes(itemId)
            ? 'file'
            : (selectedItems.database.includes(itemId)
                    ? 'database'
                    : 'none'
            )
    }

    return (
        <div>
            <div className="mb-2 d-flex align-items-center">
                <Button size="lg" className="px-4">Apply</Button>
                <div className="ms-auto d-flex gap-2">
                    <Button
                        onClick={handleSelectAllFileClick}
                        variant="outline-secondary"
                    >Select All File</Button>
                    <Button
                        onClick={handleSelectAllDatabaseClick}
                        variant="outline-secondary"
                    >Select All Database</Button>
                    <Button
                        onClick={handleClearAllClick}
                        variant="outline-secondary"
                    >Clear All</Button>
                </div>
            </div>

            {items.map(item => (
                <ConflictItem
                    key={item.hash}
                    item={item}
                    currentSelectType={itemSelectionType(item)}
                    onSelectionChange={
                        (selectionType) => handleItemSelectionChange(item, selectionType)
                    }
                />
            ))}
        </div>
    )
}

export default App;