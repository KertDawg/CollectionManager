import { Platform } from "quasar";
import axios from "axios";
import router from "../router";


var APIObject = {
  GetAPIRoot: function ()
  {
    var APIRoot;


    if (process.env.DEV)
    {
      APIRoot = "http://localhost:8087/api/";
    }
    else
    {
      APIRoot = "/api/";
    }

    return APIRoot;
  },

  get: function (URL, store)
  {
    store.dispatch("user/LoadCookie");

    return new Promise(function (resolve, reject)
    {
      axios.get(APIObject.GetAPIRoot() + URL, { headers: { Token: store.getters["user/IsLoggedIn"] ? store.getters["user/GetUser"].Token : "" } })
        .then(function (response)
        {
          resolve(response.data);
        })
        .catch(function (error)
        {
          if (error.response)
          {
            if (error.response.status === 401)
            {
              store.commit("user/LogOutUser");
              router.push("/home");
            }
          }

          reject(error);
        });
    });
  },

  post: function (URL, Data, store)
  {
    store.dispatch("user/LoadCookie");

    return new Promise(function (resolve, reject)
    {
      axios.post(APIObject.GetAPIRoot() + URL, Data, { headers: { Token: store.getters["user/IsLoggedIn"] ? store.getters["user/GetUser"].Token : "" } })
        .then(function (response)
        {
          resolve(response.data);
        })
        .catch(function (error)
        {
          if (error.response)
          {
            if (error.response.status === 401)
            {
              store.commit("user/LogOutUser");
              router.push("/home");
            }
          }

          reject(error);
        });
    });
  },

  check: function(store)
  {
    return new Promise (function (resolve, reject) 
    {
      APIObject.post("user/check", {}, store).then(function (response)
      {
        if (response === 0)
        {
          reject("Invalid user.  Maybe the token timed out from inactivity.");
        }
        else
        {
          var ShouldWeReload = APIObject.CompareAppVersions(store, response.AppVersion);
          store.commit("user/SetUser", { User: response });
          store.dispatch("user/SetCookie");

          if (ShouldWeReload)
          {
            //  Reload the app.
            document.location.reload();
          }

          resolve(response);
        }
      }).catch(function(error)
      {
        reject(error);
      }
      );
    });
  },

  CompareAppVersions: function(store, NewAppVersion)
  {
    //  Get the existing app version from store.
    var CurrentAppVersion = store.getters["user/GetAppVersion"];

    if (CurrentAppVersion !== NewAppVersion)
    {
      //  The app versions don't match.
      //  Update the cookie.
      store.commit("user/SetAppVersion", NewAppVersion);

      return true;
    }

    return false;
  }
};


export default APIObject;
