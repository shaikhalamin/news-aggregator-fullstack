import { FeedFilterFormFields } from "../components/home/feedFilterHelpers";

export const formatFilterObject = (data: FeedFilterFormFields) => {
  const filteredObj: FeedFilterFormFields = {
    ...data,
  };

  if (data.source) {
    filteredObj.source = data.source;
  }

  if (data.startDate && data.endDate) {
    filteredObj.startDate = new Date(filteredObj?.startDate as string)
      .toISOString()
      .slice(0, 10);
    filteredObj.endDate = new Date(filteredObj?.endDate as string)
      .toISOString()
      .slice(0, 10);
  }
  return filteredObj;
};
