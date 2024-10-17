import * as yup from "yup";
import { isNull } from "lodash";
import { FeedList, UserFeed } from "@/app/types/user/UserFeed";

export type FeedFilterFormFields = {
  q: string;
  startDate?: string;
  endDate?: string;
  source?: string;
  category?: string;
};

export const FeedFilterSchema = yup
  .object({
    q: yup.string().required("Please enter any keyword").nullable(),
    startDate: yup.string().optional().nullable(),
    endDate: yup.string().optional().nullable(),
    source: yup.string().optional().nullable(),
    category: yup.string().optional().nullable(),
  })
  .required();

export const prepareFeedResponse = (response: any) => {
  const responseList = response?.data?.data;
  const feedList: FeedList = {
    data: responseList?.data as UserFeed[],
    meta: {
      total: responseList?.total as number,
      per_page: responseList?.per_page as number,
      page: responseList?.current_page as number,
      lastPage: responseList?.last_page as number,
    },
  };

  return feedList;
};


