import { FeedPreferancePayloadType } from "@/app/types/feedpreferance";
import { axiosPrivate } from "../lib/axios-private";

export const setFeedPreferance = async (payload: FeedPreferancePayloadType) => {
  return axiosPrivate.post("/user-preferences", payload, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};


export const getFeedPreferance = async () => {
    return axiosPrivate.get("/user-preferences", {
      headers: {
        "Content-Type": "application/json",
      },
    });
  };

