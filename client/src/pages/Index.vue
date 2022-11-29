<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Who are you?</div>
        </div>
      </q-card-section>
      <q-card-section>
        You should log in.
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>

import api from "../utils/api";
import error from "../utils/error";
import notification from "../utils/notification";


export default {
  name: "PageIndex",

  data()
  {
    return {
      Items: [],
    };
  },

  mounted()
  {
    //  Log the user in if there's a cookie.
    this.$store.dispatch("user/LoadCookie");

    if (this.$store.getters["user/IsLoggedIn"])
    {
      this.$router.push("/home");
    }
  },

  methods:
  {
  },
};

</script>
