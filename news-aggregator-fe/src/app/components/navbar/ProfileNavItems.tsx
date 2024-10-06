import React from "react";
import { Nav, Dropdown } from "react-bootstrap";
import { MdDashboard, MdLogout } from "react-icons/md";
import { FaUserCog } from "react-icons/fa";
import { getLocalSession, removeLocalSession } from "@/app/api/local-storage";
import { User } from "@/app/types/user/User";
import { authLogOut } from "@/app/api/services/auth";
import { FE_BASE } from "@/app/api/api-urls";

const ProfileNavItems = () => {
  const session = getLocalSession();
  const user = session ? (session.user as User) : null;

  if (!session) {
    return <>No user session found !</>;
  }

  const logOut = () => {
    authLogOut().then(()=>{
      removeLocalSession()
      window.location.href = `${FE_BASE}/auth/signin` as string;
    })
  };

  return (
    <Nav>
      <Dropdown align={"end"}>
        <Dropdown.Toggle
          variant="dark"
          id="dropdown-basic"
          className="text-white"
        >
          <span className="ft-15 fw-normal">{user?.first_name} {user?.last_name}</span>
        </Dropdown.Toggle>
        <Dropdown.Menu>
          <Dropdown.Item href="/profile">
            <span style={{ marginRight: "3px" }}>
              <FaUserCog size={19} className="text-info" />
            </span>
            <span className="ft-14 fw-normal">Profile</span>
          </Dropdown.Item>
          <Dropdown.Item href="/settings">
            <span style={{ marginRight: "3px" }}>
              <MdDashboard size={19} className="text-info" />
            </span>
            <span className="ft-14 fw-normal">Settings</span>
          </Dropdown.Item>
          <Dropdown.Item onClick={() => logOut()}>
            <span style={{ marginRight: "3px" }}>
              <MdLogout size={19} className="text-danger" />
            </span>
            <span className="ft-14 fw-normal">Logout</span>
          </Dropdown.Item>
        </Dropdown.Menu>
      </Dropdown>
    </Nav>
  );
};

export default ProfileNavItems;
