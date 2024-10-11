"use client";
import React from "react";
// import { SSRProvider } from "react-bootstrap";
import NewsAgNavbar from "../navbar/NewsAgNavbar";
import BaseContainer from "../common/container/BaseContainer";

type SSRChildrenType = {
  children: React.ReactNode;
};

const SSRLayout: React.FC<SSRChildrenType> = ({ children }) => {
  return (
    <>
      <NewsAgNavbar />
      <BaseContainer>{children}</BaseContainer>
      <footer className="py-4">
        
      </footer>
    </>
  );
};

export default SSRLayout;
