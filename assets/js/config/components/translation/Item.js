import React, {useContext, useState} from "react";
import {Button, Card, Form, Placeholder} from "react-bootstrap";
import GlobalsContext from "../../context/GlobalsContext";
import Icon from "../Icon";
import useTranslationValidator from "../../hooks/useTranslationValidator";
import DomainField from "./DomainField";
import useMutateItem from "../../hooks/useMutateItem";

const Item = React.memo(({translation, removeEmptyItem}) => {
    const [editing, setEditing] = useState(false);
    const showForm = editing || !translation.id;

    const handleEditClick = () => setEditing(true);
    const handleCloseFormClick = () => {
        if (!translation.id) {
            removeEmptyItem(translation);
        }
        setEditing(false);
    }

    const handleEditToggle = () => {
        setEditing(editing => !editing)
    }

    return (
        <div className={`translation-item mb-2 ${translation.active ? '' : 'inactive'}`}>
            {showForm
                ? <ItemForm
                    item={translation}
                    handleClose={handleCloseFormClick}
                    handleEditToggle={handleEditToggle}
                />
                : <ItemText
                    item={translation}
                    handleEdit={handleEditClick}
                    handleEditToggle={handleEditToggle}
                />
            }
        </div>
    );
});

const ItemText = ({item, handleEdit, handleEditToggle}) => {
    const {booleanLabel} = useContext(GlobalsContext);

    return (
        <Card>
            <Card.Header onClick={handleEditToggle} role="button">
                <div className="row align-items-center">
                    <div className="col-sm-7">{item.code}</div>
                    <div className="col-sm-3 text-muted text-end">
                        {item.domain}
                    </div>
                    <div className="col-sm-2 text-end">
                        <b>Active:</b> {booleanLabel(item.active)}
                    </div>
                </div>
            </Card.Header>
            <Card.Body>
                <div className="row align-items-start">
                    <div className="col-sm-10">
                        {Object.entries(item.values).map(([locale, value]) => (
                            <div key={locale} className="d-flex">
                                <div className="me-2 mt-1 text-end" style={{minWidth: 25}}>{locale}</div>
                                <pre className="flex-fill trans-value">{value}</pre>
                            </div>
                        ))}
                    </div>
                    <div className="d-grid gap-2 col-sm-2">
                        <Button variant="outline-secondary" size="sm" onClick={handleEdit}>
                            <Icon icon="pencil-square"/>
                            Edit
                        </Button>
                    </div>
                </div>
            </Card.Body>
        </Card>
    );
};

const getFormValues = (defaultLocales, item) => {
    const itemValues = {...item?.values || {}};
    const values = {};

    defaultLocales.forEach(locale => {
        values[locale] = itemValues[locale] || '';
    });

    return {...values, ...itemValues};
}

const ItemForm = ({item, handleClose, handleEditToggle}) => {
    const {booleanLabel, domains, locales: defaultLocales} = useContext(GlobalsContext);

    const {save: saveItem, isLoading} = useMutateItem()

    const [formData, setFormData] = useState(() => ({
        code: item.code,
        domain: item.domain,
        values: getFormValues(defaultLocales, item),
    }));
    const [showErrors, setShowErrors] = useState(false);
    const {valid, errors} = useTranslationValidator(formData);
    const isNew = !item.id;

    const handleCodeChange = (e) => {
        if (isNew) {
            setFormData({...formData, code: e.target.value});
        }
    };

    const handleDomainChange = (domain) => {
        if (isNew) {
            setFormData({...formData, domain});
        }
    };

    const handleValueChange = (locale, e) => {
        const newValues = {...formData.values};
        newValues[locale] = e.target.value;
        setFormData({...formData, values: newValues});
    };

    const save = (formData) => {
        saveItem({...item, ...formData}).then(() => handleClose())
    }

    const handleDeactivateClick = () => save({active: false});
    const handleActivateClick = () => save({active: true});

    const handleSaveClick = () => {
        if (valid) {
            save(formData)
        } else {
            setShowErrors(true);
        }
    };

    return (
        <Card>
            <Card.Header onClick={isNew ? null : handleEditToggle} role="button">
                <div className="row align-items-center">
                    <div className="col-sm-7 m-0 d-flex">
                        {isNew
                            ?
                            <Form.Control
                                value={formData.code}
                                onChange={handleCodeChange}
                                size="sm"
                                disabled={!isNew}
                            />
                            : formData.code
                        }
                        {isLoading && <span className="ms-3">Loading...!</span>}
                    </div>
                    <div className="col-sm-3 text-muted text-end">
                        {isNew
                            ? (
                                <DomainField
                                    domains={domains}
                                    value={formData.domain}
                                    disabled={!isNew}
                                    selectDomain={handleDomainChange}
                                />
                            ) : (
                                formData.domain
                            )
                        }
                    </div>
                    <div className="col-sm-2 text-end">
                        <b>Active:</b> {booleanLabel(item.active)}
                    </div>
                </div>
            </Card.Header>
            <Card.Body>
                <div className="row align-items-start">
                    <div className="col-sm-10">
                        {Object.entries(formData.values).map(([locale, value]) => (
                            <div key={locale} className="d-flex mb-2">
                                <div className="me-2 mt-1 text-end" style={{minWidth: 25}}>{locale}</div>
                                <Form.Control
                                    as="textarea"
                                    value={value}
                                    onChange={e => handleValueChange(locale, e)}
                                    size="sm"
                                />
                            </div>
                        ))}
                    </div>
                    <div className="d-grid gap-2 col-sm-2">
                        <Button variant="primary" onClick={handleSaveClick}><Icon icon="save"/>Save</Button>
                        <Button variant="danger" onClick={handleClose}><Icon icon="x"/>Cancel</Button>

                        <hr/>

                        {!isNew && (
                            item.active
                                ? <Button size="sm" variant="warning" onClick={handleDeactivateClick}>
                                    <Icon icon="trash"/>Deactivate
                                </Button>
                                : <Button size="sm" variant="success" onClick={handleActivateClick}>
                                    <Icon icon="check-circle"/>Activate
                                </Button>
                        )}
                    </div>
                </div>
                {showErrors ? <ItemFormErrors errors={errors}/> : null}
            </Card.Body>
        </Card>
    );
};

const ItemFormErrors = ({errors = {}}) => {
    return (
        <div>
            {Object.keys(errors).length > 0
                ? (
                    <ul>
                        {Object.entries(errors).map(([key, messages]) => (
                            <li key={key} className="text-danger">
                                <b className="text-capitalize">{key}:</b> {messages.join(', ')}
                            </li>
                        ))}
                    </ul>
                ) : null
            }
        </div>
    );
};

const LoadingItem = () => {
    return (
        <div className="translation-item mb-2">
            <Card>
                <Card.Header>
                    <div className="row align-items-center">

                        <Card.Title className="col-sm-7 m-0">
                            <Placeholder xs={7}/>
                        </Card.Title>

                        <div className="col-sm-3 text-muted text-end">
                            <Placeholder xs={11}/>
                        </div>
                        <div className="col-sm-2 text-end">
                            <Placeholder xs={11}/>
                        </div>
                    </div>
                </Card.Header>
                <Card.Body>
                    <div className="row align-items-start">
                        <div className="col-sm-10 col-lg-11">
                            <div className="d-flex gap-2 mb-3">
                                <Placeholder className="pb-4" xs={1}/>
                                <Placeholder className="pb-4" xs={11}/>
                            </div>
                            <div className="d-flex gap-2 mb-3">
                                <Placeholder className="pb-4" xs={1}/>
                                <Placeholder className="pb-4" xs={11}/>
                            </div>
                            <div className="d-flex gap-2 mb-0">
                                <Placeholder className="pb-4" xs={1}/>
                                <Placeholder className="pb-4" xs={11}/>
                            </div>
                        </div>
                        <div className="d-grid gap-2 col-sm-2 col-lg-1">
                            <Placeholder.Button xs={12}/>
                        </div>
                    </div>
                </Card.Body>
            </Card>
        </div>
    );
};

export {LoadingItem};
export default Item;