import { SignUpFormFields } from "@/app/components/auth/helpers";
import { axiosPrivate } from "../lib/axios-private";
import { axiosPublic } from "../lib/axios-public";

export const getUsers = async () => {
  return axiosPrivate.get("/users", {
    headers: {
      "Content-Type": "application/json",
    },
  });
};

export const createUser = async (userPayload: SignUpFormFields) => {
  return axiosPublic.post("/users", userPayload, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};

export const updateUser = async (id: number, formData: any) => {
  return axiosPrivate.post(`/users/${id}`, formData, {
    headers: {
      Accept: "application/json",
      "Content-Type": "multipart/form-data",
    },
  });
};

export const getUser = async (id: number) => {
  return axiosPrivate.get(`/users/${id}`, {
    headers: {
      "Content-Type": "application/json",
    },
  });
};

export const deleteUser = async (id: number) => {
  return axiosPrivate.delete(`/users/${id}`);
};

