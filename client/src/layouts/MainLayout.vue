<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated>
      <q-toolbar class="glossy">
        <q-btn flat round dense size="xl" icon="collections_bookmark" to="/home" />

        <q-toolbar-title>Collections Manager</q-toolbar-title>

        <q-btn flat round dense icon="settings" to="/settings" v-if="$store.getters['user/IsLoggedIn']" />
        <q-btn flat round dense icon="rowing" @click="LogOutClick" v-if="$store.getters['user/IsLoggedIn']" />
        <q-btn flat round dense icon="waving_hand" @click="LogInClick" v-if="!$store.getters['user/IsLoggedIn']" />
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view v-slot="{ Component }">
        <transition enter-active-class="animated flipInX"
                    leave-active-class="animated flipOutX"
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
          <q-btn label="Login" color="primary" @click="LogInDialogClick"></q-btn>
          <q-btn label="Cancel" @click="LogInCancelClick"></q-btn>
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
      this.UserName = "";
      this.Password = "";
      this.$store.commit("user/LogOutUser");
      this.$router.push("/home");
    },

    LogInDialogClick: function ()
    {
      api.post("user/login", { UserName: this.UserName, Password: this.Password }, this.$store).then((response) =>
      {
        if (response.UserID == -1)
        {
          //  This is a failed login.
          this.UserName = "";
          this.Password = "";

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
      });/*.catch((e) =>
      {
        error.HandleError("Login error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
      });*/
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
