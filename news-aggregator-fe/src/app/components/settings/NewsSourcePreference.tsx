"use client";
import React, { useState, useEffect, SyntheticEvent } from "react";
import {
  Form,
  Row,
  Col,
  Card,
  Container,
  Button,
  Table,
} from "react-bootstrap";
import { FormProvider, useFieldArray, useForm } from "react-hook-form";
import { isNull } from "lodash";
import { yupResolver } from "@hookform/resolvers/yup";
import SubmitButton from "../common/form/SubmitButton";
import { InputField } from "../common/form/InputField";
import { getErrorMessage } from "@/app/utils/auth";
import SelectField from "../common/form/SelectField";
import { getNewsCategoriesBySource } from "@/app/api/services/search-filters";

import { UserFeed } from "@/app/types/user/UserFeed";
import {
  NewsSourceFormFields,
  NewsSourceSchema,
} from "./sourcePreferenceHelpers";
import {
  FeedPreferancePayloadType,
  FeedPreference,
} from "@/app/types/feedpreferance";
import { preparePreferencePayload } from "@/app/utils/feed-preferance";
import {
  getFeedPreferance,
  getFeedPreferanceByNewsSource,
  setFeedPreferance,
} from "@/app/api/services/feed-preferance";

type CategoryType = {
  id: string;
  name: string;
};

export const NewsSourcePreference = () => {
  const [submitLoading, setSubmitLoading] = useState<boolean>(false);
  const [checkedCategories, setCheckedCategories] = useState<string[]>([]);
  const [prefList, setPrefList] = useState<FeedPreference[]>([]);

  useEffect(() => {
    getFeedPreferance().then((res) => {
      setPrefList(res?.data?.data);
    });
  }, []);

  const reactHookFormMethods = useForm<NewsSourceFormFields>({
    resolver: yupResolver(NewsSourceSchema),
    mode: "onTouched",
    defaultValues: {
      authors: [],
    },
  });

  const {
    handleSubmit,
    setError,
    setValue,
    reset,
    control,
    formState: { errors },
  } = reactHookFormMethods;

  const { fields, append, remove } = useFieldArray({
    control,
    name: "authors",
  });

  const errorMessage = getErrorMessage(errors);

  const sourceNameList = [
    {
      id: "nytimes_api",
      name: "Nytime Api",
    },
    {
      id: "guardian_api",
      name: "Gurdian Api",
    },
    {
      id: "news_api_org",
      name: "News Api Org",
    },
  ];

  const [categories, setCategories] = useState<CategoryType[]>([]);

  const selectOnNewsSourceChange = async (source: string) => {
    setCheckedCategories([]);
    const categoriesResponse = await getNewsCategoriesBySource(source);
    const sourceCategories = categoriesResponse?.data?.data?.map(
      (category: string) => {
        return {
          id: category,
          name: category.charAt(0).toUpperCase() + category.slice(1),
        };
      }
    );
    setCategories(() => sourceCategories);

    getFeedPreferanceByNewsSource(source).then((userPreference) => {
      const preferenceMetaData = userPreference.data.data;
      if (
        !isNull(preferenceMetaData) &&
        !isNull(preferenceMetaData.metadata?.categories)
      ) {
        const userCheckedCategories = preferenceMetaData.metadata
          ?.categories as string[];
        let updatedList = [...checkedCategories];
        if (userCheckedCategories.length > 0) {
          updatedList.splice(0, updatedList.length, ...userCheckedCategories);
          setCheckedCategories(updatedList);
          setValue("categories", updatedList);
        }
      }

      if (
        !isNull(preferenceMetaData) &&
        !isNull(preferenceMetaData.metadata?.authors)
      ) {
        const defaultAuthors = preferenceMetaData.metadata?.authors;
        setValue("authors", defaultAuthors);
      }
    });
  };

  const handleSearchPreferenceList = (preference: FeedPreference) => {
    const findIndex = prefList.findIndex(
      (list: FeedPreference) => list.id === preference.id
    );

    if (findIndex != -1) {
      prefList.splice(findIndex, 1, preference);
      setPrefList((prev: FeedPreference[]) => [...prev]);
    } else {
      setPrefList((prev: FeedPreference[]) => [...prev, preference]);
    }
  };

  const onSubmit = async (data: NewsSourceFormFields) => {
    const payload = preparePreferencePayload(data);
    setSubmitLoading(true);
    setFeedPreferance(payload)
      .then((res) => {
        const preferance = res?.data?.data as FeedPreference;
        handleSearchPreferenceList(preferance);
      })
      .finally(() => {
        setSubmitLoading(false);
      });
  };

  const handleFeature = (e: SyntheticEvent) => {
    const target = e.target as HTMLInputElement;
    let updatedList = [...checkedCategories];
    if (target.checked) {
      updatedList = [...checkedCategories, target.value];
    } else {
      updatedList.splice(checkedCategories.indexOf(target.value), 1);
    }
    setCheckedCategories(updatedList);
    setValue("categories", updatedList);
  };

  return (
    <>
      <Container>
        <Row className="py-5">
          <Col>
            <h4 className="py-2 text-center">Create news feed preference</h4>
            <Card className="border border-0">
              <Card.Body>
                <Row>
                  <Col md={{ span: 8, offset: 2 }}>
                    <FormProvider {...reactHookFormMethods}>
                      <Form className="py-2" onSubmit={handleSubmit(onSubmit)}>
                        <Row className="mb-2">
                          <Col md="6">
                            <SelectField
                              labelText="Source"
                              fieldName="source"
                              selectData={sourceNameList}
                              selectOnChange={selectOnNewsSourceChange}
                              errorMessage={errorMessage("source")}
                            />
                          </Col>
                        </Row>

                        <Row className="py-2">
                          <h6>
                            Category [Select source to populate source category]
                          </h6>

                          {categories.length > 0 &&
                            categories.map((category, index) => {
                              return (
                                <Col md="3" key={index} className="mt-2">
                                  <Form.Group controlId={`htmlId`}>
                                    <Form.Check
                                      type={"checkbox"}
                                      className={``}
                                    >
                                      <Form.Check.Input
                                        type={"checkbox"}
                                        value={category.id}
                                        checked={checkedCategories.includes(
                                          category.id
                                        )}
                                        onChange={handleFeature}
                                      />
                                      <Form.Check.Label className={`ft-13`}>
                                        <span className={``}>
                                          {category.name}
                                        </span>
                                      </Form.Check.Label>
                                    </Form.Check>
                                  </Form.Group>
                                </Col>
                              );
                            })}
                        </Row>

                        <Row className="py-2">
                          <Col md="4">
                            <Button
                              variant="info"
                              className="mt-3"
                              onClick={() => append({ name: "" })}
                            >
                              + Add Author
                            </Button>
                          </Col>
                        </Row>

                        <Row className="py-2">
                          <Col md={8}>
                            {fields.map((field, index) => {
                              return (
                                <Row
                                  key={index}
                                  className="mb-1"
                                  // eslint-disable-next-line react/jsx-no-duplicate-props
                                  key={field.id}
                                >
                                  <Col md="10" sm="10">
                                    <InputField
                                      labelText="Author"
                                      inputType="text"
                                      name={`authors.${index}.name` as const}
                                      placeholder="Enter author name"
                                      errorMessage={errorMessage("author")}
                                    />
                                  </Col>
                                  <Col md="2" sm="2">
                                    <div className="mt-3">
                                      <Button
                                        variant="danger"
                                        className="mt-3"
                                        onClick={() => remove(index)}
                                      >
                                        x
                                      </Button>
                                    </div>
                                  </Col>
                                </Row>
                              );
                            })}
                          </Col>
                        </Row>

                        <Row className="mb-2">
                          <Col md="4">
                            <div className="mt-3">
                              <SubmitButton
                                title="Submit"
                                isLoading={submitLoading}
                                loadingTitle=""
                                buttonCls="w-100 mt-3 signup-btn"
                                variant="primary"
                                isDisabled={submitLoading}
                              />
                            </div>
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

        <Row className="py-2">
          <Col md={{ span: 8, offset: 2 }}>
            <Table striped bordered hover>
              <thead>
                <tr>
                  <th>Source</th>
                  <th>Categories</th>
                  <th>Authors</th>
                </tr>
              </thead>
              <tbody>
                {prefList.length > 0 &&
                  prefList.map((pref) => (
                    <>
                      <tr>
                        <td>{pref.source}</td>
                        <td>{pref?.metadata?.categories?.join(" ,")}</td>
                        <td>{pref?.metadata?.authors?.join(" ,")}</td>
                      </tr>
                    </>
                  ))}
              </tbody>
            </Table>
          </Col>
        </Row>
      </Container>
    </>
  );
};

export default NewsSourcePreference;
