import Image from "next/image";
import styles from "./page.module.css";
import { Col, Container, Row } from "react-bootstrap";
import HomeComponent from "./components/home/HomeComponent";
import PrivateLayout from "./components/layouts/PrivateLayout";

export default function Home() {
  return (
    <PrivateLayout>
      <Container>
        <Row>
          <Col>
            <HomeComponent />
          </Col>
        </Row>
      </Container>
    </PrivateLayout>
  );
}
