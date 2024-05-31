import React, { useEffect, useState } from 'react';
import SwitchButton from 'bootstrap-switch-button-react';
import { categoriesApi, getuserbyidApi, setusercategoriesApi } from '../store/actions/campaign';
import Skeleton from 'react-loading-skeleton';
import { translate } from '../utils';
import ReactPaginate from 'react-paginate';
import { useSelector } from 'react-redux';
import { selectCurrentLanguage } from '../store/reducers/languageReducer';
import { toast } from 'react-toastify';

const UserBasedCategories = () => {
    const [data, setData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [finalToggleID, setFinalToggleID] = useState("");
    const [totalLength, setTotalLength] = useState(0);
    const [offsetdata, setOffsetdata] = useState(0);
    const limit = 6;
    const currentLanguage = useSelector(selectCurrentLanguage);
    // get user by id
    useEffect(() => {
        getuserbyidApi((response) => {
            const useridData = response.data;
            // user categories
            const alluserIds = useridData.map((elem) => elem.category_id);

            // common id get
            const CommanID = [];
            for (let i = 0; i < alluserIds.length; i++) {
                const values = alluserIds[i].split(',');
            for (let j = 0; j < values.length; j++) {
                CommanID.push(values[j]);
            }
            }

            // category api call
            categoriesApi(
                offsetdata.toString(), limit.toString(),currentLanguage.id,
                (response) => {
                    setTotalLength(response.total)
                    const toggledData = response.data.map((element) => {
                        // here set isToggleOn has boolean with actual data
                        const isToggledOn = CommanID.includes(element.id.toString());
                        return { ...element, isToggledOn };
                    });
                  setData(toggledData)
                    setLoading(false);
                },
                (error) => {
                    if (error === 'No Data Found') {
                    setData('');
                    setLoading(false);
                    }
                }
                );

        }, (error) => {
            console.error(error);
        });

    }, [offsetdata,currentLanguage])




   // handle switch
    const handleSwitchChange = (id) => {

        setData((prevData) => {
            // return those toggle is true
            const newData = prevData.map((element) => {
              if (element.id === id) {
                return { ...element, isToggledOn: !element.isToggledOn };
              }
              return element;
            });
            const toggledIds = newData
            .filter((element) => element.isToggledOn)
            .map((element) => element.id);


          const finalToggleID = toggledIds.length === 0 ? 0 : toggledIds.join(',');

          setFinalToggleID(finalToggleID);
            return newData;
          });
    }

    // here final submit button
    const finalSubmit = (e) => {
      e.preventDefault();
        setusercategoriesApi(
            finalToggleID ,
          (response) => {
              toast.success(response.message);
            },
            (error) => {
              toast.error(error);
            }
        );
    }

    const handlePageChange = (selectedPage) => {
        const newOffset = selectedPage.selected * limit;
        setOffsetdata(newOffset);
      };


    // button style
    const switchButtonStyle = {
        width: '100%',
        marginLeft: '3rem',
        marginRight: '3rem',
    };

  return (
    <>
      <section className="manage_preferences py-5">
        <div className="container">
          {loading ? (
            <div>
              <Skeleton height={200} count={3} />
            </div>
          ) : (
            <>
              <div className="row">
                {data && data.length > 0 ? (
                  data.map((element, index) => (
                    <div className="col-md-4 col-12" key={index}>
                      <div className="manage_card">
                        <div className="inner_manage">
                          <div className="manage_image">
                            <img src={element.image} alt="news" />
                          </div>
                          <div className="manage_title">
                            <p className="mb-0">{element.category_name}</p>
                          </div>
                          <div className="manage_toggle">
                            <SwitchButton
                              checked={element.isToggledOn}
                              onlabel="ON"
                              onstyle="success"
                              offlabel="OFF"
                              offstyle="danger"
                              style={switchButtonStyle}
                              onChange={() => handleSwitchChange(element.id)}
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  ))
                ) : null}
              </div>
              <button className='finalsumit_btn mb-5' onClick={(e) => finalSubmit(e)}>{translate("saveLbl")}</button>
            </>
          )}
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
      </section>
    </>
  );
};

export default UserBasedCategories;
