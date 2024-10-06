"use client";

import React, { useEffect, useState } from "react";
import { Row, Col, FormText } from "react-bootstrap";

const FormCustomError: React.FC<{
  errorMessage: string;
  hookErrors: any;
}> = ({ errorMessage, hookErrors }) => {
  const [message, setMessage] = useState<string>("");

  useEffect(() => {
    setMessage(errorMessage);
  }, [errorMessage]);

  if (!message.length) {
    return <></>;
  }

  return (
    <Row className="py-3">
      <Col md={{ span: 6, offset: 3 }}></Col>
      {<FormText className="text-danger">{message}</FormText>}
    </Row>
  );
};

export default FormCustomError;
