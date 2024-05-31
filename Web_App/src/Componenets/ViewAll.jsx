import React from "react";
import { useState } from "react";
import { Link, useParams } from "react-router-dom";
import { useEffect } from "react";
import BreadcrumbNav from "./BreadcrumbNav";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { getfeaturesectionbyidApi } from "../store/actions/campaign";
import Skeleton from "react-loading-skeleton";
import { convertToSlug, translate } from "../utils";
import no_image from "../images/no_image.jpeg";
import ReactPaginate from "react-paginate";

const ViewAll = () => {
    const [Data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentPage, setCurrentPage] = useState(0);
    const dataPerPage = 6; // number of posts per page
    const pagesVisited = currentPage * dataPerPage;
    const { slug,id } = useParams();
    const catid = id;
    const categoryName = slug;

     // handle page change
     const handlePageChange = ({ selected }) => {
        setCurrentPage(selected);
    };


    const currentLanguage = useSelector(selectCurrentLanguage);

    useEffect(() => {
        getfeaturesectionbyidApi(
            catid,
            "",
            "",
            (response) => {
                setData(response.data);
                setLoading(false);
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setLoading(false);
                }
            }
        );
    }, [catid, currentLanguage]);

    // slice the array to get the current posts

    const currentData = Data[0]?.news ? (Data[0]?.news.slice(pagesVisited, pagesVisited + dataPerPage)) : (Data[0]?.breaking_news.slice(pagesVisited, pagesVisited + dataPerPage));

    const lengthdata = Data[0]?.news ? Data[0]?.news.length : Data[0]?.breaking_news.length

    return (
        <>
            {Data[0]?.news ? <>
                <BreadcrumbNav SecondElement={Data[0].title} ThirdElement="0" />
                <div id="BNV-main">
                    <div id="BNV-content" className="container">
                        {loading ? (
                            <div>
                                <Skeleton height={200} count={3} />
                            </div>
                        ) : (
                            <div className="row">
                                {currentData ? (
                                    currentData.map((element) => (
                                        <div className="col-md-4 col-12" key={element.id}>
                                            <Link id="Link-all" to={`/news/${element.id}/${element.category_id}`}>
                                                <div id="BNV-card" className="card">
                                                    <img id="BNV-card-image" src={element.image ? element.image : no_image} className="card-img" alt="..." />
                                                    <div id="BNV-card-body" className="card-body">
                                                        {/* <button id='BNV-btnCatagory' className='btn btn-sm' type="button" >{element.category_name}</button> */}
                                                        <h5 id="BNV-card-title" className="card-title">
                                                            {element.title}
                                                        </h5>

                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center my-5">{translate("nodatafound")}</div>
                                )}
                            </div>
                        )}
                        <ReactPaginate
                            previousLabel={translate("previous")}
                            nextLabel={translate("next")}
                            pageCount={Math.ceil(lengthdata / dataPerPage)}
                            onPageChange={handlePageChange}
                            containerClassName={"pagination"}
                            previousLinkClassName={"pagination__link"}
                            nextLinkClassName={"pagination__link"}
                            disabledClassName={"pagination__link--disabled"}
                            activeClassName={"pagination__link--active"}
                        />
                    </div>
                </div>
            </> : null};

            {Data[0]?.breaking_news ? <>
                <BreadcrumbNav SecondElement={Data[0].title} ThirdElement="0" />
                <div id="BNV-main">
                    <div id="BNV-content" className="container">
                        {loading ? (
                            <div>
                                <Skeleton height={200} count={3} />
                            </div>
                        ) : (
                            <div className="row">
                                {currentData ? (
                                    currentData.map((element) => (
                                        <div className="col-md-4 col-12" key={element.id}>
                                            <Link id="Link-all" to={`/breaking-news/${element.id}`}>
                                                <div id="BNV-card" className="card">
                                                    <img id="BNV-card-image" src={element.image ? element.image : no_image} className="card-img" alt="..." />
                                                    <div id="BNV-card-body" className="card-body">
                                                        {/* <button id='BNV-btnCatagory' className='btn btn-sm' type="button" >{element.category_name}</button> */}
                                                        <h5 id="BNV-card-title" className="card-title">
                                                            {element.title}
                                                        </h5>
                                                    </div>
                                                </div>
                                            </Link>
                                        </div>
                                    ))
                                ) : (
                                    <div className="text-center my-5">{translate("nodatafound")}</div>
                                )}
                            </div>
                        )}
                        <ReactPaginate
                            previousLabel={translate("previous")}
                            nextLabel={translate("next")}
                            pageCount={Math.ceil(lengthdata / dataPerPage)}
                            onPageChange={handlePageChange}
                            containerClassName={"pagination"}
                            previousLinkClassName={"pagination__link"}
                            nextLinkClassName={"pagination__link"}
                            disabledClassName={"pagination__link--disabled"}
                            activeClassName={"pagination__link--active"}
                        />
                    </div>
                </div>
            </> : null};

        </>
    );
}

export default ViewAll;
