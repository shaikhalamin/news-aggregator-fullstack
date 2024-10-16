"use client";

import React, { useCallback, useEffect, useState } from "react";
import SingleNewsItem from "./SingleNewsItem";
import { FilterType, FilterTypeInitialVal } from "@/app/types/feedtypes";
import { getUserFeed } from "@/app/api/services/feed";
import { FeedList, UserFeed } from "@/app/types/user/UserFeed";
import {
  Row,
  Col,
  Spinner,
  Container,
  Card,
  Form,
  Button,
} from "react-bootstrap";
import {
  FeedFilterFormFields,
  FeedFilterSchema,
  prepareFeedResponse,
} from "./feedFilterHelpers";
import { FormProvider, useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { getErrorMessage } from "@/app/utils/auth";
import { getNewsCategoriesBySource } from "@/app/api/services/search-filters";
import { InputField } from "../common/form/InputField";
import CustomDatePicker from "../common/form/CustomDatePicker";
import SelectField from "../common/form/SelectField";
import SubmitButton from "../common/form/SubmitButton";
import { formatFilterObject } from "@/app/utils/filter";
import {
  createFilterUrl,
  formFieldsToKeyValue,
  generateQueryFilterUrl,
} from "@/app/utils/api";
import CustomPagination from "../common/pagination/CustomPagination";

export type BasicType = {
  page: number;
  perPage: number;
};

const HomeComponent = () => {
  const [feedList, setFeedList] = useState<FeedList>({
    data: [] as UserFeed[],
    meta: {
      total: 0,
      per_page: 0,
      page: 0,
      lastPage: 0,
    },
  } as FeedList);
  const [filterClient, setFilterClient] = useState(false);
  const [active, setActive] = useState(1);
  const [loading, setLoading] = useState(false);
  const [customFilter, setCustomFilter] =
    useState<FilterType>(FilterTypeInitialVal);

  const [submitLoading, setSubmitLoading] = useState<boolean>(false);

  const reactHookFormMethods = useForm<FeedFilterFormFields>({
    resolver: yupResolver(FeedFilterSchema),
    mode: "onTouched",
  });

  const {
    handleSubmit,
    reset,
    formState: { errors },
  } = reactHookFormMethods;

  const errorMessage = getErrorMessage(errors);

  const sourceNameList = [
    {
      id: "guardian_api",
      name: "Gurdian Api",
    },
    {
      id: "nytimes_api",
      name: "Nytime Api",
    },
    {
      id: "news_api_org",
      name: "News Api Org",
    },
  ];

  const [categories, setCategories] = useState([]);

  const selectOnChange = (value: string) => {
    getNewsCategoriesBySource(value).then((response) => {
      const sourceCategories = response?.data?.data?.map((category: string) => {
        return {
          id: category,
          name: category.charAt(0).toUpperCase() + category.slice(1),
        };
      });
      setCategories(sourceCategories);
    });
  };

  useEffect(() => {
    setLoading(true);
    getUserFeed()
      .then((res) => {
        setLoading(false);
        const feedList = prepareFeedResponse(res);
        setFeedList(feedList);
      })
      .catch((err) => {
        setLoading(false);
        console.log("data fetch error", err);
      });
  }, []);

  const onSubmit = async (data: FeedFilterFormFields) => {
    const filterObject = formatFilterObject(data);
    const removedFalsy = formFieldsToKeyValue(filterObject);
    filterClient === false && setFilterClient(true);
    setCustomFilter((prevState) => {
      return {
        ...prevState,
        filters: {
          ...removedFalsy,
        },
      };
    });

    console.log("removedFalsy", removedFalsy);

    //const { filterUrl } = generateQueryFilterUrl(filterObject);

    // if (filterUrl.length > 2) {
    //   setSubmitLoading(true);
    //   setLoading(true);
    //   getUserFeed(filterUrl)
    //     .then((res) => {
    //       setLoading(false);
    //       setSubmitLoading(false);
    //       const feedList = prepareFeedResponse(res);
    //       setFeedList(feedList);
    //     })
    //     .catch((error) => {
    //       setSubmitLoading(false);
    //       setLoading(false);
    //       console.log("error : ", error);
    //     });
    // }
  };

  useEffect(() => {
    if (filterClient) {
      setLoading(true);
      const queryUrl = createFilterUrl(customFilter);
      getUserFeed(queryUrl)
        .then((res) => {
          setLoading(false);
          const feedList = prepareFeedResponse(res);
          setFeedList(feedList);
        })
        .catch((err) => {
          setLoading(false);
          console.log("data fetch error", err);
        });
    }
  }, [filterClient, customFilter, customFilter.basic, customFilter.filters]);

  const handlePagination = useCallback(
    (page: number) => {
      filterClient === false && setFilterClient(true);
      page && setActive(page);
      setCustomFilter((prevState) => {
        return {
          ...prevState,
          basic: {
            ...prevState.basic,
            page: page,
          },
        };
      });
    },
    [filterClient]
  );

  const handleClearFilter = () => {
    setFilterClient(true);
    setActive(1);
    setCustomFilter(() => FilterTypeInitialVal);
    reset();
  };

  return (
    <>
      <Container>
        <Row className="py-5">
          <Col>
            <Card className="border border-0">
              <Card.Body>
                <Row>
                  <Col md={{ span: 8, offset: 2 }}>
                    <FormProvider {...reactHookFormMethods}>
                      <Form className="py-2" onSubmit={handleSubmit(onSubmit)}>
                        <Row className="mb-2">
                          <Col md="4">
                            <InputField
                              labelText="Filter"
                              inputType="text"
                              name="q"
                              placeholder="Search any keywords"
                              errorMessage={errorMessage("q")}
                            />
                          </Col>
                          <Col md="4">
                            <CustomDatePicker
                              labelText="Start Date"
                              name="startDate"
                              placeholderText="Select start date"
                              maxDate={new Date()}
                              errorMessage={errorMessage("startDate")}
                            />
                          </Col>
                          <Col md="4">
                            <CustomDatePicker
                              labelText="End Date"
                              name="endDate"
                              placeholderText="Select end date"
                              maxDate={new Date()}
                              errorMessage={errorMessage("endDate")}
                            />
                          </Col>
                        </Row>
                        <Row className="mb-2">
                          <Col md="4">
                            <SelectField
                              labelText="Source"
                              fieldName="source"
                              selectData={sourceNameList}
                              selectOnChange={selectOnChange}
                              errorMessage={errorMessage("source")}
                            />
                          </Col>
                          <Col md="4">
                            <SelectField
                              labelText="Category"
                              fieldName="category"
                              selectData={categories}
                              errorMessage={errorMessage("category")}
                            />
                          </Col>
                          <Col md="4">
                            <Row>
                              <Col md="6">
                                <div className="mt-3">
                                  <SubmitButton
                                    title="Search"
                                    isLoading={submitLoading}
                                    loadingTitle=""
                                    buttonCls="w-100 mt-3 signup-btn"
                                    variant="primary"
                                    isDisabled={submitLoading}
                                  />
                                </div>
                              </Col>
                              <Col md="6">
                                <div className="mt-3">
                                  <Button
                                    variant="danger"
                                    className="mt-3"
                                    onClick={() => handleClearFilter()}
                                  >
                                    Clear
                                  </Button>
                                </div>
                              </Col>
                            </Row>
                          </Col>
                        </Row>
                      </Form>
                    </FormProvider>
                  </Col>
                </Row>
              </Card.Body>
            </Card>
          </Col>
        </Row>
      </Container>
      {loading == true && (
        <Row className="py-1 px-1 mt-3">
          <Col md={{ span: 4, offset: 4 }} className="mt-1 mb-1 text-center">
            <Spinner
              as="span"
              animation="border"
              size="sm"
              role="status"
              aria-hidden="true"
            />{" "}
            Fetching....
          </Col>
        </Row>
      )}

      {loading == false && feedList.data.length > 0 && (
        <div style={{
          minHeight: '400px', 
          position: 'relative',
        }}>
          {feedList.data.map((feed, index) => {
            return <SingleNewsItem feed={feed} key={index as number} />;
          })}
        </div>
      )}

      {loading == false && feedList.data.length > 0 && (
        <>
          <hr className="mt-3" />
          <CustomPagination
            currentPage={active}
            totalPages={feedList.meta.lastPage as number}
            onPageChange={handlePagination}
          />
        </>
      )}
    </>
  );
};

export default HomeComponent;
