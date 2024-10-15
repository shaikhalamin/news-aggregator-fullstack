"use client";

import React, { useState } from "react";
import { Form, Row, Col, Card } from "react-bootstrap";
import { FormProvider, useForm } from "react-hook-form";
import { yupResolver } from "@hookform/resolvers/yup";
import { BsFillEyeFill, BsFillEyeSlashFill } from "react-icons/bs";
import { SignInFormFields, signInSchema } from "./helpers";
import {
  getErrorMessage,
  populateServerValidationError,
} from "@/app/utils/auth";
import BaseContainer from "../common/container/BaseContainer";
import { InputField } from "../common/form/InputField";
import InputGroupCustomField from "../common/form/InputGroupCustomField";
import SubmitButton from "../common/form/SubmitButton";
import { login } from "@/app/api/services/auth";
import FormCustomError from "../common/form/FormCustomError";
import { setUserSession } from "@/app/api/local-storage";
import { FE_BASE } from "@/app/api/api-urls";

const Signin = () => {
  const [submitLoading, setSubmitLoading] = useState<boolean>(false);
  const [revealed, setRevealed] = useState<boolean>(false);
  const [customeServerError, setCustomServerError] = useState<string>("");

  const reactHookFormMethods = useForm<SignInFormFields>({
    resolver: yupResolver(signInSchema),
    mode: "onTouched",
  });

  const {
    handleSubmit,
    setError,
    reset,
    formState: { errors },
  } = reactHookFormMethods;

  const errorMessage = getErrorMessage(errors);

  const onCustomError = (message: string) => {
    //alert(message)
    setCustomServerError(message);
  };

  const onSubmit = async (data: SignInFormFields) => {
    const singinPayload = {
      ...data,
    };

    try {
      setSubmitLoading(true);
      const signIn = await login(singinPayload);
      setSubmitLoading(false);
     
      if (signIn?.status === 200) {
        reset();
        setUserSession(signIn?.data?.data);
        window.location.href = `${FE_BASE}/` as string;
      }
    } catch (error: any) {
      //console.log("Login error", error)
      setSubmitLoading(false);
      populateServerValidationError<SignInFormFields>(
        error,
        setError,
        onCustomError
      );
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
                      <h5 className="text-center ft-24 fw-bold mt-4">
                        Sign in
                      </h5>
                    </Col>
                  </Row>
                  <FormProvider {...reactHookFormMethods}>
                    <FormCustomError
                      errorMessage={customeServerError}
                      hookErrors={errors}
                    />
                    <Form className="py-2" onSubmit={handleSubmit(onSubmit)}>
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
                            title="Sign in"
                            isLoading={submitLoading}
                            loadingTitle=""
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

export default Signin;
