import React, {useContext} from "react";
import Item from "./Item";
import TranslationsContext from "../../context/TranslationsContext";

export default function List() {
    const {translations} = useContext(TranslationsContext);

    return (
        <div>
            <div className="row paginator-container">
                <div className="col-sm-4 total-count"><b>Items:</b> 1</div>
                <div className="col-sm-8 text-right">
                    <nav aria-label="Page navigation">
                        <ul className="pagination">

                            <li className="disabled">
                                <span aria-hidden="true">«</span>

                            </li>

                            <li className="active">
                                <span>1</span>

                            </li>

                            <li className="disabled">
                                <span aria-hidden="true">»</span>

                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div>
                <div>
                    {translations.map(translation => (
                        <Item key={translation.id} translation={translation}/>
                    ))}
                </div>
            </div>
        </div>
    );
}