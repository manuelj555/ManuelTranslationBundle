import React, {useId} from "react";
import {Dropdown, Form} from "react-bootstrap";

const DomainField = ({domains, value, selectDomain}) => {
    const id = useId();

    const handleDomainChange = (e) => selectDomain(e.target.value);
    const handleNewDomainChange = (e) => selectDomain(e.target.value?.trim());
    const isSelected = (domain) => domain === value;

    return (
        <div>
            <Dropdown autoClose="outside">
                <Dropdown.Toggle variant="outline-secondary" size="sm">
                    {value || 'Select Domain'}
                </Dropdown.Toggle>
                <Dropdown.Menu>
                    {domains.map(domain => (
                        <Dropdown.ItemText key={domain}>
                            <Form.Check
                                name={`form-domain-${id}`}
                                id={`form-${id}-domain-${domain}`}
                                type="radio"
                                value={domain}
                                label={domain}
                                checked={isSelected(domain)}
                                onChange={handleDomainChange}
                            />
                        </Dropdown.ItemText>
                    ))}
                    <Dropdown.ItemText>
                        <Form.Control
                            size="sm"
                            placeholder="New Domain"
                            onChange={handleNewDomainChange}
                        />
                    </Dropdown.ItemText>
                </Dropdown.Menu>
            </Dropdown>
        </div>
    );
};

export default DomainField;