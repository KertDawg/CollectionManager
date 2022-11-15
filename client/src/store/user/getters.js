/*
export function someGetter (state) {
}
*/

export function GetUser(state)
{
  return state.User;
}

export function IsLoggedIn(state)
{
  return state.LoggedIn;
}

export function IsAdmin(state)
{
  if (state.Admin !== undefined)
  {
    return !!state.Admin;
  }
  else
  {
    return !!state.User.Admin
  }
}

export function GetAppVersion(state)
{
  if (state.AppVersion !== undefined)
  {
    return !!state.AppVersion;
  }
  else
  {
    return !!state.User.AppVersion
  }
}

