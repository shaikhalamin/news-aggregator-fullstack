import { axiosPrivate } from "../lib/axios-private";

export const getNewsCategoriesBySource = (source: string) => {
  return axiosPrivate.get(`/news-categories/${source}`, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};

export const getSearchResult = (filterQuery: string) => {
  return axiosPrivate.get(`/search-filter?${filterQuery}`, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};
