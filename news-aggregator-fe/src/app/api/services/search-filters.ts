import { axiosPrivate } from "../lib/axios-private";

export const getNewsCategoriesBySource = (source: string) => {
  return axiosPrivate.get(`/news-categories/${source}`, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};


