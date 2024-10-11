"use client";

import React, { useEffect, useState } from "react";
import SingleNewsItem from "./SingleNewsItem";
import { FilterType } from "@/app/types/feedtypes";
import { getUserFeed } from "@/app/api/services/feed";
import { UserFeed } from "@/app/types/user/UserFeed";
import { FeedFilterComponent } from "./FeedFilterComponent";
import { Row, Col, Spinner } from "react-bootstrap";

const HomeComponent = () => {
  const [feedList, setFeedList] = useState<UserFeed[]>([]);
  const [filterClient, setFilterClient] = useState(false);
  const [active, setActive] = useState(1);
  const [loading, setLoading] = useState(false);
  const [clearFilter, setClearFilter] = useState<boolean>()
  const [customFilter, setCustomFilter] = useState<FilterType>({
    basic: {
      page: 1,
      perPage: 30,
    },
    filters: {},
  });

  useEffect(() => {
    getUserFeed()
      .then((res) => {
        const responseList = res?.data?.data?.data;
        setFeedList(responseList);
      })
      .catch((err) => {
        console.log("data fetch error", err);
      });
  }, []);

  useEffect(() => {
    getUserFeed()
      .then((res) => {
        const responseList = res?.data?.data?.data;
        setFeedList(responseList);
      })
      .catch((err) => {
        console.log("data fetch error", err);
      });
  }, [clearFilter]);

  const onSearchFilter = (isSearching: boolean) => {
    console.log("is searching: ", isSearching);
    setLoading(isSearching);
  };

  const onSearchResult = (searchResult: UserFeed[]) => {
    setFeedList(searchResult);
  };

  const onClearFilter = (isClear: boolean) => {
    setClearFilter(isClear)
  };

  return (
    <>
      <FeedFilterComponent
        onSearchFilter={onSearchFilter}
        onSearchResult={onSearchResult}
        onClearFilter={onClearFilter}
      />
      {loading == true && (
        <Row className="py-1 px-1 mt-3">
          <Col md={{ span: 4, offset: 4 }} className="mt-1 mb-1 text-center">
            <Spinner
              as="span"
              animation="border"
              size="sm"
              role="status"
              aria-hidden="true"
            />{" "}
            Loading....
          </Col>
        </Row>
      )}

      {loading == false &&
        feedList.length > 0 &&
        feedList.map((feed, index) => {
          return <SingleNewsItem feed={feed} key={index as number} />;
        })}
    </>
  );
};

export default HomeComponent;
