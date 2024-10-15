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
      <Col md={{ span: 10, offset: 1 }}>
        <div className="alert alert-danger text-center" role="alert">
          {message}
        </div>
      </Col>
    </Row>
  );
};

export default FormCustomError;
