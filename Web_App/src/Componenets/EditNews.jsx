import React, { useEffect, useState } from "react";
import BreadcrumbNav from "./BreadcrumbNav";
import { translate } from "../utils";
import { Button, Form } from "react-bootstrap";
import DatePicker from "react-datepicker";
import { AiFillPicture, AiOutlineUpload } from "react-icons/ai";
import { SlCalender } from "react-icons/sl";
import { categoriesApi, deleteImageApi, getSubcategoryByCategoryApi, gettagsApi, setNewsApi } from "../store/actions/campaign";
import { selectLanguages } from "../store/reducers/languageReducer";
import { useSelector } from "react-redux";
import { Select, Space } from "antd";
import Dropzone from "react-dropzone";
import SwiperCore, { Navigation, Pagination } from "swiper";
import { Swiper, SwiperSlide } from "swiper/react";
import ReactQuill from "react-quill";
import { toast } from "react-toastify";
import { selectManageNews } from "../store/reducers/createNewsReducer";
import VideoPlayerModal from "./VideoPlayerModal";
import { Link, Navigate, useNavigate } from "react-router-dom";
import { BsFillPlayFill } from "react-icons/bs";
import { IoIosClose } from "react-icons/io";

const { Option } = Select;
SwiperCore.use([Navigation, Pagination]);

const EditNews = () => {

    const [showCategory, setShowCategory] = useState(false);
    const [mainImage, setMainImage] = useState(null);
    const [category, setCategory] = useState([]);
    const [showUrl, setShowURl] = useState(false);
    const [videoUrl, setVideoUrl] = useState(false);
    const [tagsData, setTagsData] = useState([]);
    const [images, setImages] = useState([]);
    const [nextStepScreen, setNextStepScreen] = useState(false);
    const [modalShow, setModalShow] = useState(false);
    const [Video_url, setVideo_url] = useState();
    const languagesData = useSelector(selectLanguages);
    const manageNews = useSelector(selectManageNews);
    const [url, setUrl] = useState(manageNews.content_value);
    const [subCategory, setSubCategory] = useState([]);
    const [showsubCategory, setShowsubCategory] = useState(false)
    const [videoData, setVideoData] = useState(manageNews.content_value);
    const [otherUrl,setOtherUrl] = useState(false);
    const navigate = useNavigate();

    const matchingObject = languagesData.find(obj => obj.id === manageNews.language_id);

    const [DefaultValue, setDefaultValue] = useState({
        defualTitle:manageNews.title,
        categorydefault: manageNews.category_name,
        standardType: manageNews.content_type,
        contentValue: manageNews.content_value,
        tagValue: manageNews.tag_name.split(","),
        dateValue: manageNews.show_till === "0000-00-00" ? null : new Date(manageNews.show_till),
        imagedefault: manageNews.image,
        languageId: manageNews.language_id,
        categoryID: manageNews.category_id,
        tagsid: manageNews.tag_id,
        contentType: manageNews.content_type,
        multipleImage: manageNews.image_data,
        subcategorydefault: manageNews.subcategory_name,
        subcategoryID: manageNews.subcategory_id,
        languageName:matchingObject.language,
        descriptionValue: manageNews.description,
    });

    

    // console.log("defauklt",DefaultValue);
    
    const handleDate = (date) => {
        setDefaultValue({ ...DefaultValue, dateValue:date })
    }

    // other multiple image
    const handleDrop = (acceptedFiles) => {
        const imageFiles = acceptedFiles.filter((file) => file.type.startsWith("image/"));

        if (acceptedFiles.length !== imageFiles.length) {
            // Some files are not images, show the error toast
            toast.error("Only image files are allowed.")
            return; // Do not proceed with adding non-image files
        }

        setImages([...images, ...acceptedFiles]);
    };

    // description content
    const handleChangeContent = (value) => {
        setDefaultValue({...DefaultValue,descriptionValue:value})
    };

    function handleVideoUrl(url) {
        setModalShow(true);
        setVideo_url(url)
    }

    // select category
    const categorySelector = (value,option) => {
        const categoryID = JSON.parse(value);
        setDefaultValue({ ...DefaultValue, categorydefault: option.label, categoryID: categoryID })
        getSubcategoryByCategoryApi(categoryID, (res) => {
            setSubCategory(res.data)
            setShowsubCategory(true)
        }, (err) => {
            if (err === "No Data Found") {
                setShowsubCategory(false)
            }
        })
    };

    const subcategorySelector = (value,option) => {
        const subcategoryID = JSON.parse(value);
        setDefaultValue({ ...DefaultValue, subcategorydefault: option.label, subcategoryID: subcategoryID })
    }

    // video selector
    const handleVideo = (e) => {
        if (e.target.files[0] && !e.target.files[0].type.includes("video")) {
            toast.error("Please select a video format")
            return true
        }
        setVideoData(e.target.files[0]);
    }

    // select post type
    const postSelector = (value) => {
        // Find the selected option in the standardPost array
        const selectedOption = standardPost.find((elem) => elem.id === value);

        if (selectedOption) {
            const contentType = selectedOption.name;
           
            if (contentType !== manageNews.content_type) {
                // Set contentValue to an empty string when the contentType changes
                setDefaultValue(DefaultValue.contentValue = "");
            }
            
            //   setContentTypeData(contentType);
            if (contentType === "standard_post") {
                setDefaultValue({ ...DefaultValue, standardType:translate("stdPostLbl"), contentType:"standard_post" })
                // DefaultValue.contentType
                setShowURl(false);
                setVideoUrl(false);
                setOtherUrl(false)
                setUrl(null);
            } else if (contentType === "video_youtube") {
                setDefaultValue({ ...DefaultValue, standardType:translate("videoYoutubeLbl"), contentType:"video_youtube" })
                setShowURl(true);
                setVideoUrl(false);
                setOtherUrl(false)
            } else if (contentType === "video_other") {
                setDefaultValue({ ...DefaultValue, standardType: translate("videoOtherUrlLbl"), contentType:"video_other" })
                setShowURl(false);
                setOtherUrl(true)
                setVideoUrl(false);
            } else if (contentType === "video_upload") {
                setDefaultValue({ ...DefaultValue, standardType:translate("videoUploadLbl"), contentType:"video_upload" })
                setShowURl(false);
                setVideoUrl(true);
                setOtherUrl(false)
            } else {
                setShowURl(false);
                setVideoUrl(false);
                setOtherUrl(false)
            }
        }

    };


    // validate url
    const validateVideoUrl = (urlData) => {
        // eslint-disable-next-line
        const videoUrlPattern = /^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/((?:watch)\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]{11})/;
        const shortsUrlPattern = /^(https?:\/\/)?(www\.)?(youtube\.com)\/(shorts)/;
        if (videoUrlPattern.test(urlData)) {
            // URL is a YouTube video
            return true;
        } else if (shortsUrlPattern.test(urlData)) {
            // URL is a YouTube Shorts video
            toast.error('YouTube Shorts are not supported');
            return false;
        }
    };

    
    // next screen step 2
    const nextStep = (e) => {
        e.preventDefault();
        
        if (DefaultValue.standardType === translate("videoUploadLbl")) {
            setUrl(videoData);
        }
            
        //url selector validation
        if (DefaultValue.standardType === translate("videoYoutubeLbl")) {
            const isYouTubeVideo = validateVideoUrl(url);
            if (!isYouTubeVideo) {
                // URL is not a YouTube video
                toast.error('URL is not a YouTube video');
                return;
            }
        } else if (DefaultValue.standardType === translate("videoOtherUrlLbl")) {
            const isYouTubeVideo = validateVideoUrl(url);
            if (isYouTubeVideo) {
                // YouTube videos are not supported for "video_other" content type
                toast.error('YouTube videos are not supported for this content type');
                return;
            }
        }

        setNextStepScreen(true);

    };



    // tag api
    useEffect(() => {
        gettagsApi(
            (response) => {
                setTagsData(response.data);
            },
            (error) => {
                console.log(error);
            }
        );
    }, []);

    // create standard post
    const standardPost = [
        {
            id: 1,
            type: translate("stdPostLbl"),
            name: "standard_post",
            param: "empty",
        },
        {
            id: 2,
            type: translate("videoYoutubeLbl"),
            name: "video_youtube",
            param: "url",
        },
        {
            id: 3,
            type: translate("videoOtherUrlLbl"),
            name: "video_other",
            param: "url",
        },
        {
            id: 4,
            type: translate("videoUploadLbl"),
            name: "video_upload",
            param: "file",
        },
    ];


    // tag
    const handleChange = (values) => {
        let tagIds = values.map((value) => {
            const selectedTag = tagsData.find((elem) => elem.tag_name === value);
            if (selectedTag) {
              return selectedTag.id;
            }
            return null;
          });

          tagIds = tagIds.filter((tagId) => tagId !== null).join(',');

          setDefaultValue((prevValue) => {
            return {
              ...prevValue,
              tagsid: tagIds,
              tagValue:values
            };
          });
    };

    // swiper other images
    const swiperOption = {
        loop: false,
        speed: 750,
        spaceBetween: 10,
        slidesPerView: 3.5,
        navigation: false,
        autoplay: false,
        breakpoints: {
            0: {
                slidesPerView: 2.5,
            },

            768: {
                slidesPerView: 2.5,
            },

            992: {
                slidesPerView: 3,
            },
            1200: {
                slidesPerView: 3.5,
            },
        },
    };

    // main image
    const handleMainImage = (e) => {
        const selectedFile = e.target.files[0];

        // Check if a file is selected
        if (!selectedFile) {
            return;
        }

        // Check if the selected file type is an image
        if (!selectedFile.type.startsWith("image/")) {
            toast.error("Please select an image file.");
            return;
        }
        e.preventDefault();
        const file = e.target.files[0];
        setDefaultValue({ ...DefaultValue, imagedefault: file });
        setMainImage(URL.createObjectURL(file));

    };

    // final submit data
    const finalSubmit = (e) => {
        e.preventDefault();
        setNewsApi(
            2,
            DefaultValue.categoryID,
            DefaultValue.subcategoryID,
            DefaultValue.tagsid,
            DefaultValue.defualTitle,
            DefaultValue.contentType,
            url,
            DefaultValue.descriptionValue,
            DefaultValue.imagedefault,
            images,
            DefaultValue.dateValue.toISOString().split("T")[0],
            DefaultValue.languageId,
            (response) => {
                toast.success(response.message)
                navigate("/manage-news")
            },
            (error) => {
                console.log("error", error);
            }
        );
    };


    // categories load as per language change
    useEffect(() => {
        // cateRef.current = null;

        categoriesApi(
            "",
            "70",
            DefaultValue.languageId,
            (response) => {
                const categoryData = response.data;
                setCategory(categoryData);
            },
            (error) => {
                if (error === "No Data Found") {
                    setCategory("");
                }
            }
        );
    }, [DefaultValue.languageId]);


     // load language data to reducer
    const languageSelector = (value) => {
        setShowCategory(true);
        // cateRef.current = null;
        const selectedData = JSON.parse(value);
        // setLanguage(selectedData.id)
        setDefaultValue((prevValue) => ({
            ...prevValue,
            languageId: selectedData.id,
            languageName: selectedData.language,
            categorydefault: null
          }));
    };

   
    
    // render first time
    useEffect(() => {
        if (matchingObject) {
            setShowCategory(true);
            setDefaultValue({ ...DefaultValue, languageId:matchingObject.id,languageName:matchingObject.language })
        }
        if (manageNews.content_type === "standard_post") {
            setDefaultValue({ ...DefaultValue, standardType:translate("stdPostLbl"), contentType:"standard_post" })
            setShowURl(false);
            setVideoUrl(false);
            setUrl(null);
        } else if (manageNews.content_type === "video_youtube") {
            setDefaultValue({ ...DefaultValue, standardType:translate("videoYoutubeLbl"), contentType:"video_youtube" })
            setShowURl(true);
            setVideoUrl(false);
        } else if (manageNews.content_type === "video_other") {
            setDefaultValue({ ...DefaultValue, standardType: translate("videoOtherUrlLbl"), contentType:"video_other" })
            setShowURl(true);
            setVideoUrl(false);
        } else if (manageNews.content_type === "video_upload") {
            setDefaultValue({ ...DefaultValue, standardType:translate("videoUploadLbl"), contentType:"video_upload" })
            setShowURl(false);
            setVideoUrl(true);
        } else {
            setShowURl(false);
            setVideoUrl(false);
        }

        getSubcategoryByCategoryApi(manageNews.category_id, (res) => {
            setSubCategory(res.data)
            setShowsubCategory(true)
        }, (err) => {
            if (err === "No Data Found") {
                setShowsubCategory(false)
            }
        })
    }, [])

    // remove image
    const handleRemoveImage = (e, id) => {
        e.preventDefault();
            deleteImageApi(id, (res) => {
                toast.success(res.message);
                const updatedImages = DefaultValue.multipleImage.filter((image) => image.id !== id);
                setDefaultValue((prevState) => ({ ...prevState, multipleImage: updatedImages }));
            }, (err) => {
                toast.error(err.message);
          });
    };

        // back button
        const Back = () => {
            setNextStepScreen(false)
        }

      
    return (

        <>
            <BreadcrumbNav SecondElement={translate("editNewsLbl")} ThirdElement="0" />
            <div className="create_news py-5 bg-white">
                <div className="container">
                    <div className="row">
                        <div className="col-md-7 col-12">
                            <img className="edit-img" src={process.env.PUBLIC_URL + "/images/manage-news.svg"} alt="create news" />
                        </div>

                        <div className="col-md-5 col-12">
                            {!nextStepScreen ? (
                                <Form onSubmit={(e) => nextStep(e)}>
                                    <div className="form_title">
                                        <p className="mb-2">{translate("editNewsLbl")}</p>
                                        <span className="mb-2">{translate("step1Of2Lbl")}</span>
                                    </div>

                                    <div className="form_details">
                                        <div className="input_form mb-2">
                                            <input type="text" defaultValue={DefaultValue.defualTitle} placeholder={translate("titleLbl")} required onChange={(e) => setDefaultValue({...DefaultValue,defualTitle:e.target.value})} />
                                        </div>
                                        <div className="dropdown_form mb-2">
                                            <Select
                                                style={{
                                                    width: "100%",
                                                }}
                                                defaultValue={DefaultValue.languageName}
                                                placeholder={translate("chooseLanLbl")}
                                                onChange={(values) => languageSelector(values)}
                                                optionLabelProp="label"
                                                >
                                                    {languagesData &&
                                                    languagesData.map((elem, id) => (
                                                        <Option value={JSON.stringify(elem)} key={id} label={elem.language}>
                                                            <Space> {elem.language}</Space>
                                                        </Option>
                                                    ))}

                                            </Select>
                                        </div>
                                        {showCategory ? (
                                            <div className="dropdown_form mb-2">
                                                    <Select
                                                        style={{
                                                            width: "100%",
                                                        }}
                                                        value={DefaultValue.categorydefault}
                                                        placeholder={translate("catLbl")}
                                                        onChange={(value, option) => categorySelector(value, option)}
                                                        optionLabelProp="label"
                                                    >

                                                    {category && category.map((elem, id) => (
                                                        <Option value={elem.id} key={id} label={elem.category_name}>
                                                            <Space> {elem.category_name}</Space>
                                                        </Option>
                                                    ))}
                                                </Select>
                                            </div>
                                        ) : null}

                                        {showsubCategory ? (
                                            <div className="dropdown_form mb-2">
                                                <Select
                                                        style={{
                                                            width: "100%",
                                                        }}
                                                        value={DefaultValue.subcategorydefault}
                                                        placeholder={translate("subcatLbl")}
                                                        onChange={(value, option) => subcategorySelector(value, option)}
                                                        optionLabelProp="label"
                                                    >

                                                    {subCategory && subCategory.map((elem, id) => (
                                                        <Option value={elem.id} key={id} label={elem.subcategory_name}>
                                                            <Space> {elem.subcategory_name}</Space>
                                                        </Option>
                                                    ))}
                                                </Select>
                                            </div>
                                        ) : null}
                                            <div className="dropdown_form mb-2">
                                                <Select
                                                    style={{
                                                        width: "100%",
                                                    }}
                                                    value={DefaultValue.standardType}
                                                    placeholder={translate("stdPostLbl")}
                                                    onChange={(value, option) => postSelector(value, option)}
                                                    optionLabelProp="label"
                                                    >


                                                    {standardPost &&
                                                    standardPost.map((elem, id) => (
                                                        <Option value={elem.id} key={id} label={JSON.stringify(elem.name)}>
                                                            {elem.type}
                                                        </Option>
                                                    ))}
                                                </Select>


                                        </div>
                                        {showUrl ? (
                                            <div className="input_form mb-2">
                                                <input type="text" defaultValue={DefaultValue.contentValue} className="inputurl" placeholder={translate("youtubeUrlLbl")} onChange={(e) => setUrl(e.target.value)} required/>
                                            </div>
                                        ) : null}
                                        {otherUrl ? (
                                            <div className="input_form mb-2">
                                                <input type="text" className="inputurl" defaultValue={DefaultValue.contentValue} placeholder={translate("otherUrlLbl")} onChange={(e) => setUrl(e.target.value)}/>
                                            </div>
                                        ) : null}
                                        {videoUrl ? (
                                            <div className="input_form mb-2 video_url">
                                                {manageNews.content_type === "video_upload" ?
                                                    <Link className="preview" onClick={() => handleVideoUrl(manageNews.content_value)}>{translate("previewLbl")}
                                                        <BsFillPlayFill />
                                                    </Link>
                                                : null}
                                                <input type="file" id="videoInput" name="video" accept="video/*" onChange={(e) => handleVideo(e)} />
                                                <label htmlFor="videoInput">
                                                    {" "}
                                                    {translate("uploadVideoLbl")}
                                                    <AiOutlineUpload className="uploadVideo" />
                                                </label>
                                                <input type="file" className="form-control" id="videoInput" name="video" accept="video/*" placeholder="Upload File" disabled hidden />
                                            </div>
                                        ) : null}

                                        <div className="dropdown_form mb-2">
                                            <Select
                                                mode="multiple"
                                                style={{
                                                    width: "100%",
                                                }}
                                                defaultValue={DefaultValue?.tagValue}
                                                placeholder={translate("tagLbl")}
                                                onChange={(values) => handleChange(values)}
                                                optionLabelProp="label"
                                            >
                                                {tagsData &&
                                                    tagsData.map((elem, id) => (
                                                        <Option key={id} value={elem.tag_name} label={elem.tag_name}>
                                                            <Space>{elem.tag_name}</Space>
                                                        </Option>
                                                    ))}
                                            </Select>
                                        </div>
                                        <div className="show_date mb-2">
                                            <DatePicker dateFormat="yyyy-MM-dd" selected={DefaultValue.dateValue} placeholderText={translate("showTilledDate")} clearButtonTitle todayButton={"Today"} minDate={new Date()} onChange={(date) => handleDate(date)} />
                                            <SlCalender className="form-calender" />
                                        </div>
                                        <div className="main_image mb-2">
                                            <input type="file" name="image" id="file" className="file_upload" onChange={(e) => handleMainImage(e)} />

                                            <label htmlFor="file">
                                                {" "}
                                                <em>
                                                {DefaultValue.imagedefault ? null : <AiFillPicture />}{" "}
                                                    {DefaultValue.imagedefault ? null : translate("uploadMainImageLbl") }
                                                </em>
                                            </label>
                                            {mainImage && (
                                                <div className="mainimage">
                                                    <img src={mainImage && mainImage} onClick={() => document.getElementById("file").click()} alt="" />
                                                </div>
                                            )}

                                           {DefaultValue.imagedefault && (
                                                <div className="mainimage">
                                                    <img src={DefaultValue.imagedefault} onClick={() => document.getElementById("file").click()} alt="" />
                                                </div>
                                            )}
                                            <input type="text"  className="form-control" placeholder="Upload File" id="file1" name="myfile" disabled hidden />
                                        </div>
                                        <div className="other_image mb-2">
                                            <Dropzone onDrop={handleDrop} multiple={true}>
                                                {({ getRootProps, getInputProps }) => (
                                                    <div {...getRootProps()} className="dropzone">
                                                        <input {...getInputProps()} className="" />
                                                        <AiFillPicture className="me-1"/> {translate("uploadOtherImageLbl")}
                                                    </div>
                                                )}
                                            </Dropzone>
                                        </div>
                                        <div className="image_slider">
                                            <Swiper {...swiperOption}>



                                            {DefaultValue.multipleImage.map((file, index) => (
                                                <SwiperSlide key={index}>
                                                    <img src={file.other_image} alt={`Uploaded ${index}`} className="all_images" />
                                                    <button onClick={(e) => handleRemoveImage(e, file.id)} className="close-btn">
                                                        <IoIosClose/>
                                                    </button>
                                                </SwiperSlide>
                                            ))}

                                            {images.map((file, index) => (
                                                <SwiperSlide key={index}>
                                                    <img src={URL.createObjectURL(file)} alt={`Uploaded ${index}`} />
                                                </SwiperSlide>
                                            ))}

                                            </Swiper>
                                        </div>
                                        <Button type="submit" className="btn btn-secondary next-btn">
                                            {translate("nxt")}
                                        </Button>
                                    </div>
                                </Form>
                            ) : (
                                <Form onSubmit={(e) => finalSubmit(e)}>
                                    <div className="form_title">
                                        <p className="mb-2">{translate("createNewsLbl")}</p>
                                        <span className="mb-2">{translate("step2of2Lbl")}</span>
                                    </div>
                                    <div className="editor">
                                        <ReactQuill defaultValue={DefaultValue.descriptionValue} onChange={handleChangeContent} />
                                    </div>
                                    <div className="row">
                                        <div className="col-md-6">
                                                <Button type="button" className="btn btn-secondary backbtn"onClick={Back}>{translate("back") }</Button>
                                            </div>
                                            <div className="col-md-6">
                                                <Button type="submit" className=" btn btn-secondary subbtn">
                                                    {translate("submitBtn")}
                                                </Button>
                                            </div>
                                              
                                    </div>
                                </Form>
                            )}
                        </div>
                    </div>
                </div>
                <VideoPlayerModal
                    show={modalShow}
                    onHide={() => setModalShow(false)}
                    // backdrop="static"
                    keyboard={false}
                        url={Video_url}
                        type_url={manageNews.content_type}
                // title={Data[0].title}
                    />
            </div>
        </>

    )
}

export default EditNews