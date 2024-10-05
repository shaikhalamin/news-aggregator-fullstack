export const getLocalSession = () => {
  const session = JSON.parse(localStorage.getItem("session") as string);
  return session;
};

export const removeLocalSession = () => {
  localStorage.removeItem("session");
};

export const setUserSession = (newSession: any) => {
  localStorage.setItem("session", JSON.stringify(newSession));
  return getLocalSession();
};

export const updateLocalSession = (updatedSession: any) => {
  const session = JSON.parse(localStorage.getItem("session") as string);
  const update = Object.assign(session, { ...updatedSession });
  setUserSession(update);
  return getLocalSession();
};
