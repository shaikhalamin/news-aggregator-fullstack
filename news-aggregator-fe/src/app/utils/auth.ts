import { AxiosError } from "axios";
import { FieldValues, Path, UseFormSetError } from "react-hook-form";

export const populateServerValidationError = <T extends FieldValues>(
  error: any,
  setError: UseFormSetError<T>
) => {
  if (error instanceof AxiosError) {
    const errors = error.response?.data?.errors;
    if (errors && Object.keys(errors).length > 0) {
      Object.keys(errors).forEach((key) => {
        const [message, _] = errors[key];
        setError(key as Path<T>, {
          type: "focus",
          message: message,
        });
      });
    }
  }
};

export const getErrorMessage = (errors: any) => {
  return (index: string) => {
    return errors && errors[index] ? errors[index]?.message : "";
  };
};
