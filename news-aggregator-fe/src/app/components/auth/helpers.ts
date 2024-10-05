import * as yup from "yup";

export type SignUpFormFields = {
  first_name: string;
  last_name: string;
  email: string;
  password: string;
};

export type SignInFormFields = {
  email: string;
  password: string;
};

export const signUpSchema = yup
  .object({
    first_name: yup.string().required("First name is required"),
    last_name: yup.string().required("Last name is required"),
    email: yup
      .string()
      .email()
      .required("Email is required")
      .typeError("The email field must be a valid email address"),
    password: yup.string().required("Password is required"),
  })
  .required();

export const signInSchema = yup
  .object({
    email: yup
      .string()
      .email()
      .required("Email is required")
      .typeError("The email field must be a valid email address"),
    password: yup.string().required("Password is required"),
  })
  .required();
