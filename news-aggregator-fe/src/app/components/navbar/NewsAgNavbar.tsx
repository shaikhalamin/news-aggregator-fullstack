"use client";

import Container from "react-bootstrap/Container";
import Nav from "react-bootstrap/Nav";
import Navbar from "react-bootstrap/Navbar";
import styles from "./navbar.module.css";
import HamBurgerIcon from "./HamBurgerIcon";
// import { useRouter } from "next/router";
import { useState } from "react";
import { NavDropdown } from "react-bootstrap";

// import ProfileNavItem from "../ProfileNavItem";

const categories = [
  {
    id: 7,
    name: "Sweets",
    alias: "sweets",
  },
  {
    id: 6,
    name: "Snacks",
    alias: "snacks",
  },
  {
    id: 5,
    name: "Buiscuits",
    alias: "biscuits",
  },
  {
    id: 4,
    name: "Cake",
    alias: "cake",
  },
  {
    id: 3,
    name: "Coockies",
    alias: "coockies",
  },
  {
    id: 2,
    name: "Bread/Bun",
    alias: "bread_bun",
  },
  {
    id: 1,
    name: "Others",
    alias: "others",
  },
];

const NewsAgNavbar = () => {
  // const router = useRouter();
  const [buttonRef, setButtonRef] = useState<number | string>("");
  // const { data: session } = useSession();
  // const { cartItems, setCartShow } = useBakeryContext();

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
            { (
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

            {/* {session && <ProfileNavItem />} */}
          </Nav>
        </Navbar.Collapse>
      </Container>
    </Navbar>
  );
};

export default NewsAgNavbar;
