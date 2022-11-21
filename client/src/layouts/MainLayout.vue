<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar class="glossy">
        <q-btn flat round dense size="xl" icon="collections_bookmark" to="/home" />

        <q-toolbar-title>Collections Manager</q-toolbar-title>

        <q-btn flat round dense icon="settings" v-if="$store.getters['user/IsLoggedIn']">
          <q-menu
            auto-close
            transition-show="jump-down"
            transition-hide="jump-up">
            <q-list bordered>
              <q-item clickable v-ripple to="/locations">
                <q-item-section avatar>
                  <q-icon name="public" />
                </q-item-section>
                <q-item-section>Locations</q-item-section>
              </q-item>
              <q-item clickable v-ripple to="/tags">
                <q-item-section avatar>
                  <q-icon name="sell" />
                </q-item-section>
                <q-item-section>Tags</q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn>
        <q-btn flat round dense icon="rowing" @click="LogOutClick" v-if="$store.getters['user/IsLoggedIn']" />
        <q-btn flat round dense icon="waving_hand" @click="LogInClick" v-if="!$store.getters['user/IsLoggedIn']" />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition enter-active-class="animated flipInY"
                    leave-active-class="animated flipOutY"
                    appear
                    :duration="300"
                    mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </q-page-container>

    <q-dialog v-model="ShowLoginDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Log In</div>
        </q-card-section>
        <q-card-section>
          <q-input v-model="UserName" label="User Name" />
          <q-input v-model="Password" label="Password" type="password" />
        </q-card-section>
        <q-card-actions>
          <q-btn label="Log In" color="primary" @click="LogInDialogClick" />
          <q-btn label="Cancel" @click="LogInCancelClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowLogoutDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Log Out</div>
        </q-card-section>
        <q-card-section>
          Are you sure you want to log out?
        </q-card-section>
        <q-card-actions>
          <q-btn label="Log Out" color="primary" @click="LogOutDialogClick" />
          <q-btn label="Cancel" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-layout>
</template>

<script>

import api from "../utils/api";
import error from "../utils/error";
import notification from "../utils/notification";


export default {
  name: "LayoutMain",

  components:
  {
  },

  data()
  {
    return {
      ShowLoginDialog: false,
      ShowLogoutDialog: false,
      UserName: "",
      Password: "",
    };
  },

  $route(to, from)
  {
    //  Log the user in if there's a cookie.
    this.$store.dispatch("user/LoadCookie");

    //  Clear any component themes.
    this.SetComponentTheme("");

    //  Make sure that the token is valid.
    api.check(this.$store)
    .catch(function (error)
    {
      //  The token has expired or something.
      this.LogOutClick();
    });
  },

  mounted()
  {
    if (this.$route.path === "/")
    {
      this.$router.push({ name: "home" });
    }

    //  Log the user in if there's a cookie.
    this.$store.dispatch("user/LoadCookie");
  },

  methods:
  {
    LogInClick: function ()
    {
      this.UserName = "";
      this.Password = "";
      this.ShowLoginDialog = true;
    },

    LogOutClick: function ()
    {
      this.ShowLogoutDialog = true;
    },

    LogOutDialogClick: function()
    {
      this.UserName = "";
      this.Password = "";
      this.ShowLogoutDialog = false;
      this.$store.commit("user/LogOutUser");
      this.$router.push("/home");
    },

    LogInDialogClick: function ()
    {
      api.post("user/login", { UserName: this.UserName, Password: this.Password }, this.$store).then((response) =>
      {
        this.UserName = "";
        this.Password = "";

        if (response.UserID == -1)
        {
          //  This is a failed login.
          notification.ShowFailure("The login was invalid.");
        }
        else
        {
          //  This is a successful login.
          this.$store.commit("user/SetUser", { User: response });
          this.$store.dispatch("user/SetCookie");
          this.ShowLoginDialog = false;
          this.$router.push("/home");
        }
      }).catch((e) =>
      {
        error.HandleError("Login error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
      });
    },

    LogInCancelClick: function ()
    {
      this.UserName = "";
      this.Password = "";
      this.ShowLoginDialog = false;
    },
  },
};

</script>

<style>

main
{
  background-image: url(~assets/Parchment.jpg);
    background-size: cover;
    background-attachment: fixed;
}


.InfoCard
{
  margin-top: 24px;
  margin-left: 8px;
  margin-right: 8px;
}

</style>
