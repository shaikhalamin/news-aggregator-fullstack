"use client";

import React, { useEffect, useState } from "react";
import SingleNewsItem from "./SingleNewsItem";
import { FilterType } from "@/app/types/feedtypes";
import { getUserFeed } from "@/app/api/services/feed";
import { UserFeed } from "@/app/types/user/UserFeed";
import { FeedFilterComponent } from "./FeedFilterComponent";

const HomeComponent = () => {
  const [feedList, setFeedList] = useState<UserFeed[]>([]);
  const [filterClient, setFilterClient] = useState(false);
  const [active, setActive] = useState(1);
  const [loading, setLoading] = useState(false);
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

  return (
    <>
    <FeedFilterComponent />
      {feedList.length > 0 &&
        feedList.map((feed) => {
          return <SingleNewsItem feed={feed} key={feed.id} />;
        })}
    </>
  );
};

export default HomeComponent;
