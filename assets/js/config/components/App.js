import React, {useCallback, useContext, useEffect, useState} from "react";
import Filter from "./translation/Filter";
import List, {LoadingList} from "./translation/List";
import axios from "axios";
import GlobalsContext from "../context/GlobalsContext";
import {v4 as uuid} from "uuid";
import Paginator from "./translation/Paginator";

const itemsPerPage = 50;

export default function App() {
    const {paths: {api: apiUrl}} = useContext(GlobalsContext);
    const [filters, setFilters] = useState(() => ({
        search: '',
        domains: [],
    }));
    const [translations, setTranslations] = useState([]);
    const [isLoading, setLoading] = useState(true);
    const [page, setPage] = useState(1);
    const [totalCount, setTotalCount] = useState(0);

    const loadTranslations = () => {
        let count = 0;

        setLoading(true);
        axios.get(apiUrl, {
            params: {
                search: filters.search,
                domains: filters.domains.filter(d => d.length > 0),
                page,
                perPage: itemsPerPage,
            }
        })
            .then(({data, headers}) => {
                count = headers['x-count'];

                return data;
            })
            .then(data => {
                setTranslations(data.map(item => ({...item, uuid: uuid()})));
                setLoading(false);
                setTotalCount(count);
            })
    }

    const applyFilters = useCallback((newFilters) => {
        setPage(1);
        setFilters({...filters, ...newFilters});
    }, [filters]);


    const changePage = useCallback((page) => {
        setPage(page);
    }, [setPage]);

    const saveItem = useCallback((item) => {
        let ajaxRequest = null;
        const itemUuid = item.uuid;

        if (item.id) {
            ajaxRequest = axios.put(apiUrl + '/' + item.id, item);
        } else {
            ajaxRequest = axios.post(apiUrl, item);
        }

        return ajaxRequest
            .then(({data}) => data)
            .then((item) => {
                setTranslations(translations => {
                    const newTranslations = [...translations];
                    const itemIndex = newTranslations.findIndex(i => i.uuid === itemUuid);
                    item = {...item, uuid: itemUuid};

                    if (0 <= itemIndex) {
                        newTranslations[itemIndex] = item;
                    } else {
                        //item nuevo
                        newTranslations.unshift(item);
                    }

                    return newTranslations;
                })
            });
    }, [apiUrl]);

    const addEmptyItem = useCallback(() => {
        setTranslations(translations => {
            const newTranslations = [...translations];
            newTranslations.unshift({
                id: null,
                uuid: uuid(),
                code: '',
                domain: 'messages',
                active: true,
                values: {},
            });

            return newTranslations;
        })
    }, []);

    const removeEmptyItem = useCallback((item) => {
        setTranslations(translations => translations.filter(({uuid}) => uuid !== item.uuid));
    }, []);

    useEffect(() => {
        loadTranslations();
    }, [filters, page]);

    const pagination = (
        <Paginator
            currentPage={page}
            totalCount={totalCount}
            onChange={changePage}
        />
    );

    return (
        <div>
            <Filter onSubmit={applyFilters}/>
            {isLoading
                ? <LoadingList/>

                : <List
                    translations={translations}
                    pagination={pagination}
                    saveItem={saveItem}
                    removeEmptyItem={removeEmptyItem}
                    addEmptyItem={addEmptyItem}
                />
            }
        </div>
    );
}