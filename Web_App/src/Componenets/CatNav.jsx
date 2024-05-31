import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { categoriesApi } from "../store/actions/campaign";
import { useSelector } from "react-redux";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import SwiperCore, { Navigation, Pagination } from "swiper";
import { Swiper, SwiperSlide } from "swiper/react";
import "swiper/swiper-bundle.css";
import { convertToSlug, translate, truncateText } from "../utils";
import Skeleton from "react-loading-skeleton";
import { settingsData } from '../store/reducers/settingsReducer';

SwiperCore.use([Navigation, Pagination]);
const CatNav = () => {
    const [Data, setData] = useState([]);
    const [isLoading, setIsLoading] = useState(true);

    const navigate = useNavigate();
    const currentLanguage = useSelector(selectCurrentLanguage);
    const categoiresOnOff = useSelector(settingsData);

    useEffect(() => {
        categoriesApi(
            "0",
            "40",
            currentLanguage.id,
            (response) => {
                const responseData = response.data;
                setData(responseData);
                setIsLoading(false);
            },
            (error) => {
                if (error === "No Data Found") {
                    setData("");
                    setIsLoading(false);
                }
            }
        );
    }, [currentLanguage]);

    const swiperOption = {
        loop: false,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: "auto",
        navigation: false,
        breakpoints: {
            1200: {
                slidesPerView: 11,
            },
        },
        autoplay: false,
    };

    return (
    <>
        { categoiresOnOff && categoiresOnOff.category_mode === "1" ?
            <div>
                {Data.length > 0 ? (
                    <div id="cn-main" expand="lg">
                        <div className="container py-2">
                            {isLoading ? (
                                <div>
                                    <Skeleton height={200} count={3} />
                                </div>
                            ) : (
                                <Swiper {...swiperOption}>
                                    {Data.slice(0, 10).map((element, index) => (
                                        <SwiperSlide key={element.id} className="text-center">
                                            <Link id="catNav-links" to={`/categories-view/${element.id}`}>
                                                <b>{truncateText(element.category_name, 8)}</b>
                                            </Link>
                                        </SwiperSlide>
                                    ))}
                                    {Data.length > 10 && (
                                        <SwiperSlide className="text-center">
                                            <button
                                                id="catNav-links"
                                                onClick={() => {
                                                    navigate("/categories");
                                                }}
                                            >
                                                {translate("More >>")}
                                            </button>
                                        </SwiperSlide>
                                    )}
                                </Swiper>
                            )}
                        </div>
                    </div>
                ) : null}
            </div>
                :
                null
        }
    </>
    );
};

export default CatNav;
