import React, {useContext, useState} from "react";
import {Button, Card, Col, Form, Row} from "react-bootstrap";
import TranslationsContext from "../../context/TranslationsContext";
import Icon from "../Icon";
import GlobalsContext from "../../context/GlobalsContext";

const emptyFilters = (domains) => ({
    search: '',
    domains: new Array(domains.length).fill(''),
});

export default function Filter() {
    const {applyFilters} = useContext(TranslationsContext);
    const {domains} = useContext(GlobalsContext);
    const [filters, setFilters] = useState(() => emptyFilters(domains));

    const handleFormSubmit = (e) => {
        e.preventDefault();
        applyFilters(filters);
    }

    const handleClearClick = (e) => {
        const newFilters = emptyFilters(domains);
        setFilters(newFilters);
        applyFilters(newFilters);
    }

    const handlerSearchChange = (e) => {
        setFilters(f => ({...f, search: e.target.value}));
    }

    const handlerDomainsChange = (index, e) => {
        setFilters(filters => {
            const domains = filters.domains;
            domains[index] = e.target.checked ? e.target.value : "";

            return {...filters, domains: domains};
        });
    }

    const isDomainSelected = (index) => {
        const value = filters.domains[index] || '';

        return '' !== value;
    }

    return (
        <Card>
            <Card.Body>
                <form onSubmit={handleFormSubmit}>
                    <div className="d-flex">
                        <div className="flex-fill px-3">
                            <Form.Group as={Row} className="mb-3">
                                <Form.Label column sm={2} lg={1}>Search</Form.Label>
                                <Col>
                                    <Form.Control value={filters.search} onChange={handlerSearchChange} type="search"/>
                                </Col>
                            </Form.Group>
                            <Form.Group as={Row} className="mb-3">
                                <Form.Label column sm={2} className="py-0" lg={1}>Domains</Form.Label>
                                <Col>
                                    {domains.map((domain, index) => (
                                        <Form.Check
                                            key={domain}
                                            id={"filter-domain-" + index}
                                            inline
                                            name="domains"
                                            label={domain}
                                            value={domain}
                                            checked={isDomainSelected(index)}
                                            onChange={e => handlerDomainsChange(index, e)}
                                        />
                                    ))}
                                </Col>
                            </Form.Group>
                        </div>
                        <div className="ms-auto">
                            <div className="d-flex gap-2">
                                <Button type="submit" variant="primary">
                                    <Icon icon="funnel"/>
                                    Apply
                                </Button>
                                <Button type="button" variant="outline-secondary" onClick={handleClearClick}>
                                    <Icon icon="x"/>
                                    Clear
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
            </Card.Body>
        </Card>
    );
}
