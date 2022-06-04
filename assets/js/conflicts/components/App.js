import React, {useState} from "react";
import ConflictItem from "./ConflictItem";
import {Button} from "react-bootstrap";
import axios from "axios";

const buildSelected = (item, type) => {
    return {
        ...item[type] || {},
        id: item.database.id || null,
        applyFor: type,
        hash: item.hash,
    }
}

const App = ({defaultItems, endpoint}) => {
    const [items, setItems] = useState(() => defaultItems);
    const [selectedItems, setSelectedItems] = useState([]);

    const changeSelectAll = (type) => {
        if (type === 'none') {
            setSelectedItems([]);
        } else if (["file", "database"].includes(type)) {
            setSelectedItems(items.map(item => buildSelected(item, type)));
        }

    }

    const handleSelectAllFileClick = () => changeSelectAll("file");
    const handleSelectAllDatabaseClick = () => changeSelectAll("database");
    const handleClearAllClick = () => changeSelectAll("none");

    const handleApplyClick = () => {
        const finished = items.length === selectedItems.length;
        const itemsToSend = selectedItems.map(item => ({
            ...item,
            applyFor: item.applyFor === 'database' ? 'local' : item.applyFor,
        }));

        axios.post(endpoint, {
            items: itemsToSend,
            finished,
        }).then(() => {
            setItems(oldItems => oldItems.filter(({database}) => (
                !selectedItems.some(({id}) => id === database.id)
            )));
            setSelectedItems([]);
        });
    };

    const handleItemSelectionChange = (item, selectionType) => {
        if (!["file", "database", "none"].includes(selectionType)) {
            return;
        }

        setSelectedItems(oldSelected => {
            const currentItemId = item.database.id;
            const newItems = oldSelected.filter(item => item.id !== currentItemId);

            if ('none' === selectionType) {
                return newItems;
            }

            newItems.push(buildSelected(item, selectionType));

            return newItems;
        });
    }

    const itemSelectionType = (item) => {
        const itemId = item.database.id;

        return selectedItems.find(({id}) => id === itemId)?.applyFor || 'none';
    }

    const selectedItemsCount = selectedItems.length;

    if (items.length === 0) {
        return null;
    }

    return (
        <div>
            <h2>Conflict Items</h2>

            <div className="mb-2 d-flex align-items-center">
                <Button
                    size="lg"
                    className="px-4"
                    onClick={handleApplyClick}
                    disabled={0 === selectedItemsCount}
                >Apply</Button>
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