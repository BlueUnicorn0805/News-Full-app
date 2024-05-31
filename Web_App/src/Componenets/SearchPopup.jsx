import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { SetSearchPopUp } from "../store/stateSlice/clickActionSlice";
import { store } from "../store/store";
import { Link, useNavigate } from "react-router-dom";
import { getnewsApi } from "../store/actions/campaign";
import { AiOutlineClose } from "react-icons/ai";
import { convertToSlug, translate, truncateText } from "../utils";

const SearchPopup = () => {
    const [Data, setData] = useState([]);
    const searchPopUp = useSelector((state) => state.clickAction.searchPopUp);

    const [searchValue, setSearchValue] = useState("");

    const navigate = useNavigate();

    // popup
    const actionSearch = () => {
        store.dispatch(SetSearchPopUp(!searchPopUp));
        setSearchValue("");
    };

    // input value
    const handleInputChange = (event) => {
        setSearchValue(event.target.value);
    };

    // news api for search
    useEffect(() => {
        getnewsApi(
            "0",
            "5",
            "",
            searchValue,
            (response) => {
                if (searchValue !== "") {
                    setData(response.data);
                } else {
                    setData([]);
                }
            },
            (error) => {
                if (error === "No Data Found") {
                    setData([]);
                }
            }
        );
    }, [searchValue]);

    // redirect news page
    const redirectPage = (e, element) => {
        actionSearch();
        e.preventDefault();
        navigate(`/news/${element.id}/${element.category_id}`);
        setSearchValue("");
    };

    return (
        <>
            {/* search popup start*/}
            <div className={searchPopUp ? "body-overlay active" : "body-overlay"} id="body-overlay" onClick={actionSearch} />
            <div className={searchPopUp ? "td-search-popup active" : "td-search-popup"} id="td-search-popup">
                <div className="search-form">
                    <div className="form-group">
                        <input type="text" className="form-control" placeholder="Search....." value={searchValue} onChange={handleInputChange} />
                    </div>
                    <button type="submit" className="submit-btn" onClick={() => setSearchValue("")}>
                        <AiOutlineClose />
                    </button>
                    <div id="ts-main" className="search_bar">
                        <div id="ts-content" className="">
                            <div className="row mx-auto">
                                {searchValue !== "" &&
                                    Data &&
                                    Data.length > 0 &&
                                    Data.map((element) => (
                                        <div className="col-12" key={element.id}>
                                            <Link id="Link-all" onClick={(e) => redirectPage(e, element)}>
                                                <div id="ts-card" className="card">
                                                    <img id="ts-card-image" src={element.image} className="card-img" alt="..." />
                                                    <div id="ts-card-body" className="card-body">
                                                        <h5 id="ts-card-title" className="card-title">
                                                            {truncateText(element.title, 150)}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    ))}
                                {searchValue !== "" && Data && Data.length === 0 && <p className="text-dark bg-white p-4 text-center">{translate("nodatafound")}</p>}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {/* search popup end*/}
        </>
    );
};

export default SearchPopup;
