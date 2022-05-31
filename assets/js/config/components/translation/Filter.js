import React from "react";
import {Card, Col, Form, Row} from "react-bootstrap";

export default function Filter() {
    return (
        <Card>
            <Card.Body>
                <form>
                    <div className="d-flex">
                        <div className="flex-fill px-3">
                            <Form.Group as={Row} className="mb-3">
                                <Form.Label column sm={2}>Search</Form.Label>
                                <Col sm={10}>
                                    <Form.Control type="search"/>
                                </Col>
                            </Form.Group>
                            <Form.Group as={Row} className="mb-3">
                                <Form.Label column sm={2}>Status</Form.Label>
                                <Col sm={10}>
                                    <Form.Check label="Inactives"/>
                                </Col>
                            </Form.Group>
                        </div>
                        <div className="ms-auto">
                            <div className="d-flex gap-2">
                                <button type="submit" className="btn btn-primary">
                                    <i className="bi bi-funnel"></i> Apply
                                </button>
                                <a className="btn btn-outline-secondary" href="#">
                                    <i className="bi bi-x"></i> Clear
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </Card.Body>
        </Card>
    );
}
