import * as yup from "yup";



export type NewsSourceFormFields = {
  source: string;
  categories?: string[];
  authors?: {
    name: string;
  }[];
};

export const NewsSourceSchema = yup
  .object({
    source: yup.string().required(),
    authors: yup.array(
      yup.object({
        name: yup.string().required(),
      })
    ).optional(),

    categories: yup.array().of(yup.string()).optional()
    // metadata: yup
    //   .object()
    //   .shape({
    //     categories: yup.array().of(yup.string()).optional(),
    //     authors: yup.array().of(yup.string()).optional(),
    //   })
    //   .optional(),
  })
  .required();
