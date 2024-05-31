import React, { useState } from "react";
import { IoArrowForwardCircleSharp } from "react-icons/io5";
import { useEffect } from "react";
import { selectCurrentLanguage } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import BreadcrumbNav from "./BreadcrumbNav";
import { getpagesApi } from "../store/actions/campaign";
import { Modal } from "antd";
import Skeleton from "react-loading-skeleton";
import { translate } from "../utils"

const MorePages = () => {
    const [Data, setData] = useState([]);
    const currentLanguage = useSelector(selectCurrentLanguage);
    const [loading, setLoading] = useState(true);
    const [modalOpen, setModalOpen] = useState(false);
    const [modalData, setmodalData] = useState(null);

    useEffect(() => {
        getpagesApi(
            (response) => {
                let allData = response.data;
                setData(allData);
                setLoading(false);
            },
            (error) => {
                setData("");
                setLoading(false);
                console.log(error);
            }
        );
    }, [currentLanguage]);

    const handleModalActive = (e,element) => {
        e.preventDefault();
        setModalOpen(true)
        setmodalData(element)

    }

    return (
        <>
            <BreadcrumbNav SecondElement={translate("More Pages")} ThirdElement="0" />
            <div className="morepages py-5 bg-white">
                <div className="container">
                    <div className="row">
                    {Data && Data.map((element) => (
                        <div className="col-md-4 col-12 mb-4">
                            <div  key={element.id} className="card" onClick={(e) => handleModalActive(e,element)}>
                                <div className="more-cat-section-card-body" >
                                    <h5 id="cat-card-text" className="card-text mb-0">
                                        {element.title}
                                    </h5>
                                    <button id="btn-cat-more" className="btn" type="button">
                                        <IoArrowForwardCircleSharp size={40} />
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                    </div>
                </div>
            </div>

            <Modal centered className="custom-modal" open={modalOpen} maskClosable={false} onOk={() => setModalOpen(false)} onCancel={() => setModalOpen(false)} footer={false} id="modaltp">
            {loading ? <Skeleton height={400} /> : <div>{modalData && modalData.page_content ? <p id="pp-modal-body" className="p-3 mb-0" dangerouslySetInnerHTML={{ __html: modalData && modalData.page_content }}></p> : <p className="noData">{translate("nodatafound")}</p>}</div>}
        </Modal>
        </>
    );
}

export default MorePages;
