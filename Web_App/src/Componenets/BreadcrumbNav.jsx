import React from "react";
import Breadcrumb from "react-bootstrap/Breadcrumb";
import { Link } from "react-router-dom";
import { FaHome } from "react-icons/fa";
import { translate } from "../utils";

function BreadcrumbNav(props) {
    return (
        <Breadcrumb id="bcb-main">
            <div className="container">
                <div className="breadcrumb_data">
                    <Breadcrumb.Item id="bcb-item">
                        <Link style={{ textDecoration: "none" }} id="bcb-link-text" to="/">
                            <FaHome size={25} id="bcb-home-logo" /> {translate("home")}
                        </Link>
                    </Breadcrumb.Item>
                    <Breadcrumb.Item active id="bcb-active-item">
                        {props.SecondElement}
                    </Breadcrumb.Item>
                    {props.ThirdElement === "0" ? null : (
                        <Breadcrumb.Item active id="bcb-third-item">
                            {props.ThirdElement}
                        </Breadcrumb.Item>
                    )}

                    {/* <Breadcrumb.Item active>Data</Breadcrumb.Item> */}
                </div>
            </div>
        </Breadcrumb>
    );
}

export default BreadcrumbNav;
