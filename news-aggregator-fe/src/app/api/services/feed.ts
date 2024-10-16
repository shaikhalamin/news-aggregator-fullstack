import { axiosPrivate } from "../lib/axios-private";

export const getUserFeed = async (filterQuery: string = "") => {
  const feedUrl = `/user-feeds?${filterQuery}`;

  return axiosPrivate.get(feedUrl, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};
