import React, { useState } from "react";
import ReactPaginate from "react-paginate";
import { IoArrowForwardCircleSharp } from "react-icons/io5";
import { useEffect } from "react";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import { categoriesApi } from "../store/actions/campaign";
import BreadcrumbNav from "./BreadcrumbNav";
import { Link } from "react-router-dom";
import { settingsData } from "../store/reducers/settingsReducer";
import { convertToSlug, translate } from "../utils";

const Categories = () => {
    const [Data, setData] = useState([]);
    const currentLanguage = useSelector(selectCurrentLanguage);
    const categoiresOnOff = useSelector(settingsData);
    const [totalLength, setTotalLength] = useState(0);
    const [offsetdata, setOffsetdata] = useState(0);
    const limit = 12;

    useEffect(() => {
        categoriesApi(
            offsetdata.toString(), limit.toString(),currentLanguage.id,
            (response) => {
                setTotalLength(response.total)
                const responseData = response.data;
                setData(responseData);
            },
            (error) => {
                // console.log(error);
            }
        );
    }, [currentLanguage, offsetdata]);

    const handlePageChange = (selectedPage) => {
        const newOffset = selectedPage.selected * limit;
        setOffsetdata(newOffset);
    };

    return (
        <>
            <BreadcrumbNav SecondElement="Categories" ThirdElement="0" />
            {categoiresOnOff && categoiresOnOff.category_mode === "1" ?
                <div className="container my-5">
                    <div className="row">
                        {Data && Data.map((element) => (
                            <div className="col-md-4 col-12 mb-4">
                                <Link id="cat-section-card" key={element.id} className="card" to={`/categories-view/${element.id}`}>
                                    <img id="cat-section-card-image" src={element.image} className="card-img" alt="..." />
                                    <div id="cat-section-card-body" className="card-img-overlay">
                                        <h5 id="cat-card-text" className="card-text mb-0">
                                            {element.category_name}
                                        </h5>
                                        <button id="btn-cat-more" className="btn" type="button">
                                            <IoArrowForwardCircleSharp size={40} />
                                        </button>
                                    </div>
                                </Link>
                            </div>
                        ))}
                    </div>
                    <ReactPaginate
                        previousLabel={translate("previous")}
                        nextLabel={translate("next")}
                        breakLabel="..."
                        breakClassName="break-me"
                        pageCount={Math.ceil(totalLength / limit)}
                        marginPagesDisplayed={2}
                        pageRangeDisplayed={5}
                        onPageChange={handlePageChange}
                        containerClassName={"pagination"}
                        previousLinkClassName={"pagination__link"}
                        nextLinkClassName={"pagination__link"}
                        disabledClassName={"pagination__link--disabled"}
                        activeClassName={"pagination__link--active"}
                    />
                </div>
                : <>
                    <div className="text-center my-5">{translate("disabledCategory")}</div>
                </>}
        </>
    );
}

export default Categories;
