import Image from "next/image";
import styles from "./page.module.css";
import { Col, Container, Row } from "react-bootstrap";

export default function Home() {
  return (
    <>
      <Container>
        <Row>
          <Col>
            <h2>Test container</h2>
          </Col>
        </Row>
      </Container>
    </>
  );
}
