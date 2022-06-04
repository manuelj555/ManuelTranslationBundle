import React, {useId} from "react";
import {Card, Form} from "react-bootstrap";

const ConflictItem = ({item, onSelectionChange, currentSelectType}) => {
    const selectFileTarget = () => onSelectionChange("file");
    const selectDatabaseTarget = () => onSelectionChange("database");

    return (
        <Card className="mb-2">
            <Card.Header>
                <div className="d-flex align-items-center">
                    <Card.Title className="mb-0">{item.file.code}</Card.Title>
                    <div className="ms-auto d-flex gap-4">
                        <div className="">{item.file.domain}</div>
                    </div>
                </div>
            </Card.Header>
            <Card.Body>
                <div className="d-flex gap-2 align-items-stretch">
                    <ValuesInfo
                        selected={currentSelectType === 'file'}
                        onSelect={selectFileTarget}
                        itemId={item.hash}
                        type="file"
                        values={item.file.values}
                    />
                    <ValuesInfo
                        selected={currentSelectType === 'database'}
                        onSelect={selectDatabaseTarget}
                        itemId={item.hash}
                        type="database"
                        values={item.database.values}
                    />
                </div>
            </Card.Body>
        </Card>
    )
}

const ValuesInfo = ({itemId, type, values, selected, onSelect}) => {
    const valueItems = Object.entries(values || {});
    const id = useId();

    const handleCheckChange = () => onSelect();

    return (
        <div className="border rounded p-2 col">
            <div className="d-flex border-bottom text-secondary text-opacity-50 mb-2 text-capitalize">
                <div>{type} Values</div>
                <Form.Check
                    type="radio"
                    checked={selected}
                    onChange={handleCheckChange}
                    name={`check-${itemId}`}
                    id={`check-${id}-use-${type}`}
                    className="ms-auto"
                    label={`Use ${type}`}
                />
            </div>
            <div>
                {valueItems.map(([locale, value]) => (
                    <div className="row mb-1" key={locale}>
                        <div className="col-sm-1 text-secondary text-opacity-75 text-end">
                            {locale.toUpperCase()}
                        </div>
                        <div className="col-sm-11">{value}</div>
                    </div>
                ))}
            </div>
        </div>
    )
}

export default ConflictItem;