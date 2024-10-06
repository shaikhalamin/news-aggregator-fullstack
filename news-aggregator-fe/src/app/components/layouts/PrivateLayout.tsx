"use client";

import { getLocalSession } from "@/app/api/local-storage";
import React from "react";
import { useRouter } from "next/navigation";

type PrivateRoute = {
  children: React.ReactNode;
};

const PrivateLayout: React.FC<PrivateRoute> = ({ children }) => {
  const router = useRouter();
  const session = getLocalSession();
  if (!session) {
    router.push("/auth/signin");
    return;
  }

  return <>{children}</>;
};

export default PrivateLayout;
