<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <div class="fullscreen text-center q-pa-md flex flex-center" v-if="!PageLoaded">
      <q-spinner-grid
        color="primary"
        size="10em"
      />
    </div>

    <q-card v-masonry-tile v-if="PageLoaded" class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Items</div>
        </div>
      </q-card-section>
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <q-input 
              label="Filter"
              v-model="TextFilter"
              clearable
              @update:model-value="TextFilterChange" />
          </div>
        </div>
        <div class="row q-pt-lg" v-if="TagFilter !== ''">
          <div class="col-md-8">
            Tag:
            <q-chip
                v-if="TagFilter !== ''"
                class="truncate-chip-labels"
                outline
                :style="{ 'background-color': TagFilter.ColorCode, 'color': TagFilter.TextCode, 'width': 'auto' }">
              <q-icon class="ChipIcon" :style="{ 'color': TagFilter.TextCode }" :name="TagFilter.IconCode" />
              {{ TagFilter.TagName }}
            </q-chip>
          </div>
          <div class="col-md-4">
            <q-btn 
              icon="cancel"
              flat
              dense
              @click="TagFilter = ''; SelectTagFilter();"
              style="color: #00000050;"
            />
          </div>
        </div>
        <div class="row q-pt-lg">
          <div class="col-12">
            <q-select v-model="TagFilter"
                    :options="TagsOrdered"
                    transition-show="scale"
                    transition-hide="scale"
                    options-dense
                    option-value="TagID"
                    option-label="TagName"
                    @update:model-value="SelectTagFilter">
              <template v-slot:option="{ itemProps, opt }">
                <q-item v-bind="itemProps">
                  <q-item-section>
                    <div>
                      <q-chip
                          class="truncate-chip-labels"
                          outline
                          :style="{ 'background-color': opt.ColorCode, 'color': opt.TextCode, 'width': 'auto', 'margin-left': opt.LeftPadding }">
                        <q-icon class="ChipIcon" :style="{ 'color': opt.TextCode }" :name="opt.IconCode" />
                        {{ opt.TagName }}
                      </q-chip>
                    </div>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" label="Add a New Item" to="/item/new" />
      </q-card-actions>
    </q-card>

    <q-card v-masonry-tile class="InfoCard ItemCard" v-for="i in FilteredItems" :key="i.ItemID">
      <q-expansion-item
        expand-separator
        switch-toggle-side
        :dense="$q.screen.lt.sm"
        :label="i.ItemName"
        @after-show="RedrawMasonry"
        @after-hide="RedrawMasonry"
      >
        <template v-slot:header>
          <div v-if="!$q.screen.lt.sm" class="row" style="width: 100%">
            <div class="col-2 q-mr-md" v-for="l in i.Locations" :key="l.LocationID" :style="{ 'background-color': l.ColorCode, 'color': l.TextCode, }">
              <q-avatar>
                <q-icon :name="l.IconCode" class="LocationIcon" size="xl" />
                <q-tooltip :delay="1000">{{ l.LocationName }}</q-tooltip>
              </q-avatar>
            </div>

            <div class="col-7">
              <q-item-label class="LargeItemName">
                {{ i.ItemName }}
              </q-item-label>
              <q-item-label>
                <q-chip v-for="t in i.Tags" :key="t.TagID" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }"
                        class="glossy" :color="t.ColorCode" :text-color="t.TextCode" :icon="t.IconCode">
                  {{ t.TagName }}
                </q-chip>
              </q-item-label>
            </div>

            <div class="col-2 PhotoColumn">
              <img :src="i.Photos[0].PhotoData" class="HeaderPhoto" v-if="i.Photos.length > 0" />
            </div>
          </div>

          <div v-if="$q.screen.lt.sm" style="width: 100%">
            <div class="row" clickable :to="'/item/' + i.ItemID">
              <div class="col-12">
                <q-item-label class="SmallItemName">
                  {{ i.ItemName }}
                </q-item-label>
              </div>
            </div>
            <div class="row" clickable :to="'/item/' + i.ItemID">
              <div class="col-auto">
                <img :src="i.Photos[0].PhotoData" class="HeaderPhoto" v-if="i.Photos.length > 0" />
              </div>
              <div class="col-auto q-ml-xs" v-for="l in i.Locations" :key="l.LocationID" :style="{ 'background-color': l.ColorCode, 'color': l.TextCode, }">
                <q-item-label>
                  <q-avatar>
                    <q-icon :name="l.IconCode" class="LocationIcon" size="md" />
                    <q-tooltip :delay="1000">{{ l.LocationName }}</q-tooltip>
                  </q-avatar>
                </q-item-label>
                <q-item-label v-for="t in i.Tags" :key="t.TagID">
                  <q-chip :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }"
                          class="glossy" :color="t.ColorCode" :text-color="t.TextCode" :icon="t.IconCode"
                          dense size="sm">
                    {{ t.TagName }}
                  </q-chip>
                </q-item-label>
              </div>
            </div>
          </div>
        </template>

        <q-btn 
          label="Edit" 
          icon="edit" 
          color="primary" 
          rounded glossy 
          class="q-mb-md q-ml-sm" 
          @click="$router.push('/item/' + i.ItemID);" />
          
        <q-btn 
          label="View" 
          icon="visibility" 
          color="primary" 
          rounded glossy 
          class="q-mb-md q-ml-sm" 
          @click="$router.push('/view/' + i.ItemID);" />
          
        <q-carousel
          swipeable
          animated
          v-model="i.SelectedPhoto"
          v-model:fullscreen="FullScreen[i.ItemID]"
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
                :icon="FullScreen[i.ItemID] ? 'fullscreen_exit' : 'fullscreen'"
                @click="FullScreen[i.ItemID] = !FullScreen[i.ItemID];"
              />
            </q-carousel-control>
          </template>
        </q-carousel>
      </q-expansion-item>
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
      FilteredItems: [],
      FullScreen: [],
      Locations: [],
      Tags: [],
      TagNodes: [],
      TagsOrdered: [],
      TextFilter: "",
      TagFilter: "",
      PageLoaded: false,
    };
  },

  mounted()
  {
    this.PageLoaded = false;

    if (!this.$store.getters["user/IsLoggedIn"])
    {
      this.$router.push("/welcome");
    }
    else
    {
      this.LoadItems();
      this.LoadLocations();
      this.LoadTags();
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
            this.FilterItems();

            this.Items.forEach(i => { i.SelectedPhoto = 0; this.FullScreen[i.ItemID] = false; })

            this.PageLoaded = true;
          }).catch((e) =>
          {
            error.HandleError("Get items error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },

    LoadLocations: function()
    {
      api.get("location", this.$store)
          .then((response) =>
          {
            this.Locations = response.Locations;
          }).catch((e) =>
          {
            error.HandleError("Get locations error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },

    LoadTags: function()
    {
      return api.get("tag", this.$store)
          .then((response) =>
          {
            this.Tags = response.Tags;
            this.MakeTagNodes();
            this.OrderTags();
          });
    },

    RedrawMasonry: function()
    {
      setTimeout(() => { this.$redrawVueMasonry() }, 100);
    },

    FilterItems: function()
    {
      if (this.TextFilter == null)
      {
        this.TextFilter = "";
      }

      var TextFilterLC = this.TextFilter.toLowerCase();

      this.FilteredItems = this.Items.filter(i => {
        if (this.TextFilter.length > 1)
        {
          if (i.ItemName.toLowerCase().indexOf(TextFilterLC) >= 0)
          {
            return true;
          }
          else
          {
            return false;
          }
        }
        else
        {
          return true;
        }
      });

      this.FilteredItems = this.FilteredItems.filter(i => {
        if (this.TagFilter !== "")
        {
          var MatchingTags = i.Tags.filter(t => {
            return (t.TagID === this.TagFilter.TagID);
          });

          if (MatchingTags.length > 0)
          {
            return true;
          }
          else
          {
            return false;
          }
        }
        else
        {
          return true;
        }
      });

      setTimeout(() => { this.$redrawVueMasonry() }, 100);
    },

    TextFilterChange: function()
    {
      this.FilterItems();
    },

    SelectTagFilter: function()
    {
      this.FilterItems();
    },

    MakeTagNodes: function()
    {
      //  Get root tags
      this.TagNodes = this.Tags.filter(t => { return (t.ParentTagID === ""); });
      
      this.TagNodes.forEach(t => {
        this.MakeChildTags(t);
      });

      this.TagNodes.sort((a, b) => {
        if (a.TagName > b.TagName)
        {
          return 1;
        }
        else if (a.TagName < b.TagName)
        {
          return -1;
        }
        else
        {
          return 0;
        }
      });
    },

    MakeChildTags: function(Parent)
    {
      Parent.Children = this.Tags.filter(c => { return (c.ParentTagID == Parent.TagID) });
      Parent.Children.sort((a, b) => {
        if (a.TagName > b.TagName)
        {
          return 1;
        }
        else if (a.TagName < b.TagName)
        {
          return -1;
        }
        else
        {
          return 0;
        }
      });

      Parent.Children.forEach(c => { this.MakeChildTags(c); });
    },

    OrderTags: function()
    {
      this.TagsOrdered = [];

      this.TagNodes.forEach(tn => {
        tn.Level = 0;
        tn.LeftPadding = "0px";
        this.TagsOrdered.push(tn);
        this.OrderChildTags(tn, 1);
      });
    },

    OrderChildTags: function(ParentTag, Level)
    {
      ParentTag.Children.forEach(pt => {
        pt.Level = Level;
        pt.LeftPadding = (Level * 12) + "px";
        this.TagsOrdered.push(pt);
        this.OrderChildTags(pt, Level + 1);
      });
    },
  },
};

</script>

<style>

div.q-item-type div.q-item__section--side
{
  width: 24px !important;
  max-width: 24px !important;
  min-width: 24px !important;
}

</style>

<style scoped>

@media (max-width: 1023px)
{
  div.ItemCard
  {
    width: 94%;
  }
}

@media (min-width: 1024px)
{
  div.ItemCard
  {
    width: 50%;
  }
}

div.PhotoColumn
{
  text-align: right;
}

div.SmallItemName
{
  font-size: normal;
  font-weight: bold;
}

div.LargeItemName
{
  font-size: x-large;
  font-weight: bold;
}

i.ChipIcon
{
  margin-right: 8px;
  font-size: x-large;
}

img.HeaderPhoto
{
  max-width: 60px;
  max-height: 60px;
}

</style>
