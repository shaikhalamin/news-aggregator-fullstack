"use client";

import React, { useState } from "react";
import { Form, Row, Col, Card } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { BsFillEyeFill, BsFillEyeSlashFill } from "react-icons/bs";
import { SignUpFormFields, signUpSchema } from "./helpers";
import {
  getErrorMessage,
  populateServerValidationError,
} from "@/app/utils/auth";
import { createUser } from "@/app/api/services/user";
import BaseContainer from "../common/container/BaseContainer";
import { InputField } from "../common/form/InputField";
import InputGroupCustomField from "../common/form/InputGroupCustomField";
import SubmitButton from "../common/form/SubmitButton";
import { useRouter } from "next/navigation";

const SignUp = () => {
  const [submitLoading, setSubmitLoading] = useState<boolean>(false);
  const [revealed, setRevealed] = useState<boolean>(false);
  const router = useRouter();

  const reactHookFormMethods = useForm<SignUpFormFields>({
    resolver: yupResolver(signUpSchema),
    mode: "onTouched",
  });

  const {
    handleSubmit,
    setError,
    reset,
    formState: { errors },
  } = reactHookFormMethods;

  const errorMessage = getErrorMessage(errors);

  const onSubmit = async (data: SignUpFormFields) => {
    const singUpPayload = {
      ...data,
    };

    try {
      setSubmitLoading(true);
      const newUser = await createUser(singUpPayload);
      setSubmitLoading(false);
      if (newUser?.status === 201) {
        reset();
        router.push("/auth/signin");
      } else {
        alert("Something went wrong !");
      }
    } catch (error: any) {
      setSubmitLoading(false);
      populateServerValidationError<SignUpFormFields>(error, setError);
    }
  };

  const showEyeIcon = (show: boolean) => {
    if (show === false) {
      return (
        <BsFillEyeSlashFill
          size={19}
          className="text-success"
          onClick={() => setRevealed((prev) => !prev)}
        />
      );
    }
    return (
      <BsFillEyeFill
        size={19}
        className="text-success"
        onClick={() => setRevealed((prev) => !prev)}
      />
    );
  };

  return (
    <BaseContainer>
      <Row className="py-2">
        <Col md={{ span: 8, offset: 2 }}>
          <Card className="border border-0">
            <Card.Body>
              <Row>
                <Col md={{ span: 8, offset: 2 }}>
                  <Row className="py-2">
                    <Col md="12">
                      <h2 className="text-center ft-40 fw-bold">
                        Get latest news
                      </h2>
                      <h5 className="text-center ft-24 fw-bold mt-4">
                        Create your account
                      </h5>
                    </Col>
                  </Row>
                  <FormProvider {...reactHookFormMethods}>
                    <Form className="py-2" onSubmit={handleSubmit(onSubmit)}>
                      <Row className="mb-2">
                        <Col md="12">
                          <InputField
                            labelText="First name"
                            name="first_name"
                            inputType="text"
                            errorMessage={errorMessage("first_name")}
                          />
                        </Col>
                      </Row>
                      <Row className="mb-2">
                        <Col md="12">
                          <InputField
                            labelText="Last name"
                            name="last_name"
                            inputType="text"
                            errorMessage={errorMessage("last_name")}
                          />
                        </Col>
                      </Row>
                      <Row className="mb-2">
                        <Col md="12">
                          <InputField
                            labelText="Email"
                            name="email"
                            inputType="email"
                            errorMessage={errorMessage("email")}
                          />
                        </Col>
                      </Row>
                      <Row className="mb-2">
                        <Col md="12">
                          <InputGroupCustomField
                            labelText="Password"
                            labelTextIcon={showEyeIcon(revealed)}
                            name="password"
                            inputType={revealed === true ? "text" : "password"}
                            errorMessage={errorMessage("password")}
                          />
                        </Col>
                      </Row>
                      <Row className="py-3">
                        <Col md={{ span: 6, offset: 3 }}>
                          <SubmitButton
                            title="Create an account"
                            isLoading={submitLoading}
                            loadingTitle="Creating"
                            buttonCls="w-100 mt-3 signup-btn"
                            variant="primary"
                            isDisabled={submitLoading}
                          />
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
    </BaseContainer>
  );
};

export default SignUp;
