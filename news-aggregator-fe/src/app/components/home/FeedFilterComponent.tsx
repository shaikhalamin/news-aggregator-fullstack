"use client";
import React, { useState, useEffect } from "react";
import { Form, Row, Col, Card, Container, Button } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { FeedFilterFormFields, FeedFilterSchema } from "./feedFilterHelpers";
import SubmitButton from "../common/form/SubmitButton";
import { InputField } from "../common/form/InputField";
import { getErrorMessage } from "@/app/utils/auth";
import CustomDatePicker from "../common/form/CustomDatePicker";
import SelectField from "../common/form/SelectField";
import {
  getNewsCategoriesBySource,
  getSearchResult,
} from "@/app/api/services/search-filters";
import { generateQueryFilterUrl } from "@/app/utils/api";
import { formatFilterObject } from "@/app/utils/filter";
import { UserFeed } from "@/app/types/user/UserFeed";

type FeedFilterProps = {
  onSearchFilter: (isSearching: boolean) => void;
  onSearchResult: (searchResult: UserFeed[]) => void;
  onClearFilter: (isClear: boolean) => void;
};

export const FeedFilterComponent: React.FC<FeedFilterProps> = ({
  onSearchFilter,
  onSearchResult,
  onClearFilter,
}) => {
  const [submitLoading, setSubmitLoading] = useState<boolean>(false);

  const reactHookFormMethods = useForm<FeedFilterFormFields>({
    resolver: yupResolver(FeedFilterSchema),
    mode: "onTouched",
  });

  const {
    handleSubmit,
    setError,
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

  const onSubmit = async (data: FeedFilterFormFields) => {
    const filterObject = formatFilterObject(data);
    const { filterUrl, queryParams } = generateQueryFilterUrl(filterObject);

    if (filterUrl.length > 2) {
      setSubmitLoading(true);
      onSearchFilter(true);
      getSearchResult(filterUrl)
        .then((res) => {
          console.log("filter response : ", res?.data?.data);
          onSearchFilter(false);
          setSubmitLoading(false);
          onSearchResult(res?.data?.data as UserFeed[]);
        })
        .catch((error) => {
          setSubmitLoading(false);
          onSearchFilter(false);
          console.log("error : ", error);
        });
    }
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
                                    onClick={() => {
                                      onClearFilter(true);
                                      reset();
                                    }}
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
    </>
  );
};
