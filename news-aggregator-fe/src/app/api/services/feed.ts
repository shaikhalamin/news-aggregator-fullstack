import { axiosPrivate } from "../lib/axios-private";

export const getUserFeed = async () => {
  return axiosPrivate.get("/user-feeds", {
    headers: {
      "Content-Type": "application/json",
    },
  });
};
