import React from "react";

export default function Icon({icon, margin = 1}) {
    return (
        <i className={"bi bi-" + icon + " me-" + margin}></i>
    );
}