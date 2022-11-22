<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
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

    <q-card v-masonry-tile class="InfoCard col-auto" v-for="i in Items" :key="i.ItemID">
      <q-item clickable :to="'/item/' + i.ItemID">
        <q-item-section avatar v-for="l in i.Locations" :key="l.LocationID" :style="{ 'background-color': l.ColorCode, 'color': l.TextCode }">
          <q-avatar>
            <q-icon :name="l.IconCode" class="LocationIcon" size="xl" />
          </q-avatar>
        </q-item-section>

        <q-item-section>
          <q-item-label class="ItemName">{{ i.ItemName }}</q-item-label>
          <q-item-label>
            <q-chip v-for="t in i.Tags" :key="t.TagID" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }"
                    class="glossy" :color="t.ColorCode" :text-color="t.TextCode" :icon="t.IconCode">
              {{ t.TagName }}
            </q-chip>
          </q-item-label>
        </q-item-section>
      </q-item>

      <img v-for="p in i.Photos" :key="p.PhotoID" 
            :src="p.PhotoData"
            clickable :to="'/item/' + i.ItemID" />
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

<style scoped>

div.ItemName
{
  font-size: x-large;
  font-weight: bold;
}

</style>
