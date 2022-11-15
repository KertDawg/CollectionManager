import { Cookies } from "quasar";


/*
export function someAction (context) {
}
*/

export function LoadCookie (context) {
    if (Cookies.has("CollectionsUser"))
    {
        context.commit("SetUser", Cookies.get("CollectionsUser"))
    }
};

export function SetCookie (context) {
    Cookies.set("CollectionsUser", context.state, {
        expires: 7,
    });
};

export function RemoveCookie (context) {
    Cookies.remove("CollectionsUser");
};
