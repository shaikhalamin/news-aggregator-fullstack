import * as yup from "yup";

export type FeedFilterFormFields = {
  q: string;
  startDate?: string;
  endDate?: string;
  source?: string;
  category?: string;
};

export const FeedFilterSchema = yup
  .object({
    q: yup.string().required('Please enter any keyword').nullable(),
    startDate: yup.string().optional().nullable(),
    endDate: yup.string().optional().nullable(),
    source: yup.string().optional().nullable(),
    category: yup.string().optional().nullable(),
  })
  .required();
