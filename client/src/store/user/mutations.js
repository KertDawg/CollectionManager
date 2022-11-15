import { Cookies } from "quasar";

/*
export function someMutation (state) {
}
*/

export function SetUser(state, payload)
{
  state.User = payload.User;
  state.AppVersion = payload.User.AppVersion;
  state.LoggedIn = true;
  state.Admin = payload.User.Admin;
  this.dispatch("user/SetCookie", state);
}

export function LogOutUser(state)
{
  state.User = { UserName: "" };
  state.AppVersion = "";
  state.LoggedIn = false;
  state.Admin = false;
  state.IndexExpanders = {};
  this.dispatch("user/RemoveCookie");
}

export function SetAppVersion(state, payload)
{
  state.AppVersion = payload;
  this.dispatch("user/SetCookie", state);
}
