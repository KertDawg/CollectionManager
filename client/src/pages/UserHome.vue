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

    <q-card v-masonry-tile class="InfoCard ItemCard" v-for="i in Items" :key="i.ItemID">
      <q-item clickable :to="'/item/' + i.ItemID">
        <q-item-section avatar v-for="l in i.Locations" :key="l.LocationID" :style="{ 'background-color': l.ColorCode, 'color': l.TextCode }">
          <q-avatar>
            <q-icon :name="l.IconCode" class="LocationIcon" size="xl" />
            <q-tooltip :delay="1000">{{ l.LocationName }}</q-tooltip>
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

      <q-carousel
        swipeable
        animated
        v-model="i.SelectedPhoto"
        v-model:fullscreen="FullScreen"
        thumbnails
        infinite
        autoplay
        v-if="i.Photos.length > 0">
        
        <q-carousel-slide v-for="(p, index) in i.Photos" :key="p.PhotoID" :name="index" :img-src="p.PhotoData" />

        <template v-slot:control>
          <q-carousel-control
            position="bottom-right"
            :offset="[18, 18]"
          >
          <q-btn
              push round dense color="white" text-color="primary"
              :icon="FullScreen ? 'fullscreen_exit' : 'fullscreen'"
              @click="FullScreen = !FullScreen;"
            />
          </q-carousel-control>
        </template>
      </q-carousel>
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
      FullScreen: false,
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

            this.Items.forEach(i => { i.SelectedPhoto = 0; i.FullScreen = false; })
          }).catch((e) =>
          {
            error.HandleError("Get items error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },
  },
};

</script>

<style scoped>

@media (max-width: 1023px)
{
  div.ItemCard
  {
    width: 90%;
  }
}

@media (min-width: 1024px)
{
  div.ItemCard
  {
    width: 50%;
  }
}

div.ItemName
{
  font-size: x-large;
  font-weight: bold;
}

</style>
