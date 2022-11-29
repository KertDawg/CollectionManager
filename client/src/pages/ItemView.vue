<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <div class="fullscreen text-center q-pa-md flex flex-center" v-if="!PageLoaded">
      <q-spinner-grid
        color="primary"
        size="10em"
      />
    </div>

    <q-card v-masonry-tile v-if="PageLoaded" class="InfoCard col-auto ItemCard">
      <q-card-section>
        <div class="row">
          <div class="col-12">
            {{ Item.ItemName }}
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="col-md-2" v-for="l in Item.Locations" :key="l.LocationID" :style="{ 'background-color': l.ColorCode, 'color': l.TextCode, }">
              <q-avatar>
                <q-icon :name="l.IconCode" class="LocationIcon" size="xl" />
                <q-tooltip :delay="1000">{{ l.LocationName }}</q-tooltip>
              </q-avatar>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <q-chip v-for="t in Item.Tags" :key="t.TagID" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }"
                        class="glossy" :color="t.ColorCode" :text-color="t.TextCode" :icon="t.IconCode">
              {{ t.TagName }}
            </q-chip>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="ControlLabel">Description:</div>
            <div v-html="Item.ItemDescription"></div>
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto" v-for="p in Item.Photos" :key="p.PhotoID"
      clickable @click="ShowLargePhoto(p)">
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <img :src="p.PhotoData" class="Photo cursor-pointer" />
          </div>
        </div>
      </q-card-section>
    </q-card>

    <q-dialog v-model="ShowLargePhotoDialog">
      <q-card class="InfoCard col-auto LargePhotoCard">
        <div class="row">
          <div class="col-auto">
            <img :src="SelectedPhoto.PhotoData" class="LargePhoto" />
          </div>
        </div>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script>

import numeral from "numeral";
import api from "../utils/api";
import error from "../utils/error";


export default {
  name: "PageItemView",

  data()
  {
    return {
      Item: {
        ItemID: "",
        ItemName: "",
        ItemDescription: "",
        ItemCost: 0,
      },
      SelectedPhoto: {},
      ShowLargePhotoDialog: false,
      PageLoaded: false,
    };
  },

  props:  
  {
    ItemID: {
      type: String,
      default: "-1",
    },
  },

  watch:
  {
    $route(to, from)
    {
      this.PageLoaded = false;
      this.CheckProperties();
    },
  },

  mounted()
  {
    this.PageLoaded = false;
    this.CheckProperties();
  },

  methods:
  {
    CheckProperties: function()
    {
      if (this.ItemID.length > 10)
      {
        this.LoadItem();
      }
      else
      {
        this.$route.push("/home");
      }
    },

    LoadItem: function()
    {
      api.get("item/public/" + this.ItemID, this.$store)
          .then((response) =>
          {
            this.Item = response.Item;

            var Cost = numeral(this.Item.ItemCost);
            this.Item.ItemCost = Cost.format("0,0.00");
            this.PageLoaded = true;
          }).catch((e) =>
          {
            error.HandleError("Get item error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },

    ShowLargePhoto: function(Photo)
    {
      this.SelectedPhoto = Photo;
      this.ShowLargePhotoDialog = true;
    },
  },
};

</script>

<style scoped>

.ItemCard
{
  min-width: 40%;
}

div.ControlLabel
{
  padding-top: 16px;
}

i.LocationIcon
{
  padding-right: 8px;
}

div.TagTreeNode
{
  padding: 8px;
  margin-left: 16px;

}

i.TagTreeIcon
{
  padding-right: 8px;
}

i.ChipIcon
{
  margin-right: 8px;
  font-size: x-large;
}

i.RemoveChipIcon
{
  margin-left: 8px;
  font-size: x-large;
}

img.Photo
{
  max-width: 100%;
  max-height: 400px;
}

.LargePhotoCard
{
  padding: 8px;
}

img.LargePhoto
{
  max-width: 100%;
  max-height: 80vh;
}

</style>
