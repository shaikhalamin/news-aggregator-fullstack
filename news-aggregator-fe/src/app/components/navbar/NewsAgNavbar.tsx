"use client";

import Container from "react-bootstrap/Container";
import Nav from "react-bootstrap/Nav";
import Navbar from "react-bootstrap/Navbar";
import styles from "./navbar.module.css";
import HamBurgerIcon from "./HamBurgerIcon";
import { useEffect, useState } from "react";;
import { getLocalSession } from "@/app/api/local-storage";
import ProfileNavItems from "./ProfileNavItems";

const NewsAgNavbar = () => {
  const [buttonRef, setButtonRef] = useState<number | string>("");
  const session = getLocalSession();

  return (
    <Navbar
      collapseOnSelect
      expand="lg"
      bg="dark"
      variant="dark"
      className={`${styles.navBgColor} py-3`}
    >
      <Container>
        <Navbar.Brand
          href="/"
          role="general-navbar-brand-role"
          className={`${styles.ft18} ${styles.ftBold}`}
        >
          <span className="text-warning">News Aggregator</span>
        </Navbar.Brand>

        <Navbar.Toggle
          aria-controls="offcanvasNavbar"
          role="navbar-toggle-role"
          className="border-none"
        >
          <HamBurgerIcon />
        </Navbar.Toggle>

        <Navbar.Collapse id="responsive-navbar-nav">
          <Nav className="me-auto">
            <Nav.Link
              href="/"
              className={`text-white ${styles.ft14} fw-normal`}
            >
              Home
            </Nav.Link>
          </Nav>
          <Nav>
            {!session && (
              <>
                <Nav.Link
                  href="/auth/signin"
                  className={`text-white ${styles.ft14}`}
                >
                  Sign In
                </Nav.Link>
                <Nav.Link
                  href="/auth/signup"
                  className={`text-white ${styles.ft14} mr-2`}
                >
                  Sign Up
                </Nav.Link>
              </>
            )}

            {session && <ProfileNavItems />}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default NewsAgNavbar;
