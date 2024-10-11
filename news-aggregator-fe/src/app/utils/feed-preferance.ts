import { NewsSourceFormFields } from "../components/settings/sourcePreferenceHelpers";
import { FeedPreferancePayloadType } from "../types/feedpreferance";

export const preparePreferencePayload = (data: NewsSourceFormFields) => {
  let payload: FeedPreferancePayloadType = {
    source: data.source,
  };

  if (data?.categories?.length) {
    payload = {
      ...payload,
      metadata: {
        categories: data.categories,
      },
    };
  }

  if (data?.authors?.length) {
    payload = {
      ...payload,
      metadata: {
        ...payload.metadata,
        authors: data?.authors?.map((author) => author.name),
      },
    };
  }

  return payload;
};
