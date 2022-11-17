<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto" v-if="!$store.getters['user/IsLoggedIn']">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Who are you?</div>
        </div>
      </q-card-section>
      <q-card-section>
        You should log in.
      </q-card-section>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto" v-if="$store.getters['user/IsLoggedIn']">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Items</div>
        </div>
      </q-card-section>
      <q-card-section>
        See your items here.
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" label="Add a New Item" to="/item/new" />
      </q-card-actions>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto" v-if="$store.getters['user/IsLoggedIn']">
      <q-card-section>
        <q-list bordered>
          <q-item v-for="i in Items" :key="i.ItemID" clickable :to="'/item/' + i.ItemID">
            <q-item-section>
              {{ i.ItemName }}
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>

import api from "../utils/api";
import error from "../utils/error";
import notification from "../utils/notification";


export default {
  name: "PageUserHome",

  data()
  {
    return {
      Items: [],
    };
  },

  mounted()
  {
    if (!this.$store.getters["user/IsLoggedIn"])
    {
      this.$router.push("/welcome");
    }
    else
    {
      this.LoadItems();
    }
  },

  methods:
  {
    LoadItems: function()
    {
      api.get("item", this.$store)
          .then((response) =>
          {
            this.Items = response.Items;
          }).catch((e) =>
          {
            error.HandleError("Get items error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },
  },
};

</script>
