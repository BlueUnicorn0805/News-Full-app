import React, { useEffect, useState } from "react";
import { useSelector } from "react-redux";
import { loadLanguageLabels, selectCurrentLanguage, selectLanguages, setCurrentLanguage } from "../store/reducers/languageReducer";
import axios from "axios";
import { SlCalender } from "react-icons/sl";
import { HiArrowLongUp,HiArrowLongDown } from "react-icons/hi2";
import { Dropdown } from "react-bootstrap";
import { FaFacebookSquare, FaInstagram, FaLinkedin, FaTwitterSquare } from "react-icons/fa";


function WeatherCard() {
    const [weather, setWeather] = useState(null);
    // eslint-disable-next-line
    const [loading, setLoading] = useState(true);
    const currentLanguage = useSelector(selectCurrentLanguage);

    useEffect(() => {
      // eslint-disable-next-line
    navigator.geolocation.getCurrentPosition(async (position) => {
        const { latitude, longitude } = position.coords;

        // get weather information
        const response = await axios.get(
            `https://api.weatherapi.com/v1/forecast.json?key=${process.env.REACT_APP_WEATHER_API_KEY}&q=${latitude},${longitude}&days=1&aqi=no&alerts=no&lang=${currentLanguage.code}`
        );
        
      setWeather(response.data);
      setLoading(false);
    });
  }, [currentLanguage]);

    // to get today weekday name
    const today = new Date();

    const dayOfMonth = today.getDate();   // returns the day of the month (1-31)
    // const month = today.getMonth() + 1;  // returns the month (0-11); add 1 to get the correct month
    const year = today.getFullYear();    // returns the year (4 digits)

    const month = today.toLocaleString('default', { month: 'long' });

     // Assuming forecastData is an array of forecast objects
     const maxTempC = weather && weather.forecast.forecastday[0].day.maxtemp_c;
    const minTempC = weather && weather.forecast.forecastday[0].day.mintemp_c;


    const languagesData = useSelector(selectLanguages);

    // language change
    const languageChange = (name, code, id) => {
        loadLanguageLabels(code);
        setCurrentLanguage(name, code, id);
    };

    useEffect(() => {
        loadLanguageLabels(currentLanguage.code);
    }, [currentLanguage]);

    return (
        <div>
            <div id="rns-weather-card">
                <div id="weather-main-text" className="container">
                    <div className="row align-items-center">
                        <div className="col-md-6 col-12">
                            <div className="left-weather">
                                <div className="calender_icon me-2">
                                    <p className=" mb-0"><SlCalender />{`${month}`}{`${dayOfMonth}`},{`${year}`}</p>
                                </div>

                                {weather && (
                                    <>
                                        <img src={weather && weather.current.condition.icon} alt="news" className="weather_icon" />
                                        <b className="me-2">{weather && weather.current.temp_c}°C</b>
                                        <div className="left-state">
                                        <p className="location-wcard mb-0 ">
                                        {weather && weather.location && weather.location.name},
                                        {weather && weather.location && weather.location.region},
                                        {weather && weather.location && weather.location.country}
                                        </p>
                                        <p className="day-Wtype-wcard mb-0 ">
                                            <HiArrowLongUp />
                                            {maxTempC}°C <HiArrowLongDown />
                                            {minTempC}°C
                                        </p>
                                        </div>
                                    </>
                                )}
                            </div>
                        </div>
                        <div className="col-md-6 col-12">
                            <div className="right-weather">
                                <ul className="language_section">
                                    <li>
                                        <Dropdown>
                                            <Dropdown.Toggle className="language_drop">
                                                {currentLanguage.name}
                                            </Dropdown.Toggle>

                                            <Dropdown.Menu style={{ backgroundColor: "#1A2E51" }}>
                                                {languagesData &&
                                                    languagesData.map((data, index) => {
                                                        return (
                                                            <Dropdown.Item key={index} id="btnLogout" onClick={() => languageChange(data.language, data.code, data.id)}>
                                                                {data.language}
                                                            </Dropdown.Item>
                                                        );
                                                    })}
                                            </Dropdown.Menu>
                                        </Dropdown>
                                    </li>
                                </ul>
                                <div className="slash-line"></div>
                                <div className="social_media_top">
                                    {process.env.REACT_APP_FACEBOOK ? (
                                        <a target="_blank" id="social_platforms" className="me-2" href={process.env.REACT_APP_FACEBOOK} rel="noreferrer">
                                            <FaFacebookSquare />
                                        </a>
                                    ) : null}
                                    {process.env.REACT_APP_INSTAGRAM ? (
                                        <a target="_blank" id="social_platforms" className="me-2" href={process.env.REACT_APP_INSTAGRAM} rel="noreferrer">
                                            <FaInstagram />
                                        </a>
                                    ) : null}
                                    {process.env.REACT_APP_LINKEDIN ? (
                                        <a target="_blank" id="social_platforms" className="me-2" href={process.env.REACT_APP_LINKEDIN} rel="noreferrer">
                                            <FaLinkedin />
                                        </a>
                                    ) : null}
                                    {process.env.REACT_APP_TWITTER ? (
                                        <a target="_blank" id="social_platforms" className="" href={process.env.REACT_APP_TWITTER} rel="noreferrer">
                                            <FaTwitterSquare />
                                        </a>
                                    ) : null}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    );
}

export default WeatherCard;
