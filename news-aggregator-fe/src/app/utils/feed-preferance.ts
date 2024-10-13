import { NewsSourceFormFields } from "../components/settings/sourcePreferenceHelpers";
import { FeedPreferancePayloadType } from "../types/feedpreferance";

export const preparePreferencePayload = (data: NewsSourceFormFields) => {
  let payload: FeedPreferancePayloadType = {
    source: data.source,
  };

  payload = {
    ...payload,
    metadata: {
      categories: data.categories,
      authors:
        data?.authors && data?.authors.length > 0
          ? data?.authors?.map((author) => author.name)
          : [],
    },
  };

  return payload;
};
