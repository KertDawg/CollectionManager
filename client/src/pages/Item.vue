<template>
  <q-page v-masonry transition-duration="0.3s" gutter="10" stagger="0.03s">
    <div class="fullscreen text-center q-pa-md flex flex-center" v-if="!PageLoaded">
      <q-spinner-grid
        color="primary"
        size="10em"
      />
    </div>

    <q-card v-masonry-tile v-if="PageLoaded" class="InfoCard col-auto ItemCard">
      <q-card-section>
        <div class="row">
          <div class="col-6 text-h6">Item</div>
          <div class="col-6" align="right">
            <q-btn class="glossy" rounded color="primary" label="Add" icon="add_a_photo" 
              @click="SaveNewPhotoClick()"
              v-if="!NewItemMode" />
          </div>
        </div>
      </q-card-section>
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <q-input v-model="Item.ItemName"
                     label="Item Name" />
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <q-select
                v-model="Item.Locations"
                multiple
                :options="Locations"
                option-value="LocationID"
                option-label="LocationName"
                label="Locations">
                <template v-slot:option="{ itemProps, opt }">
                  <q-item v-bind="itemProps">
                    <q-item-section avatar :style="{ 'background-color': opt.ColorCode, 'color': opt.TextCode }">
                      <q-icon :name="opt.IconCode" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ opt.LocationName }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            Tags:
            <q-chip v-for="t in Item.Tags" :key="t.TagID"
              outline
              :style="{ 'background-color': t.ColorCode, 'color': t.TextCode, 'margin': '8px' }">
              <q-icon class="ChipIcon" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }" :name="t.IconCode" />
              {{ t.TagName }}
              <q-icon class="RemoveChipIcon" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }" name="cancel" @click="RemoveChip(t)" />
            </q-chip>
            <q-select v-model="NewTag"
                    :options="TagsOrdered"
                    transition-show="scale"
                    transition-hide="scale"
                    options-dense
                    option-value="TagID"
                    option-label="TagName"
                    @update:model-value="SelectNewChip">
              <template v-slot:option="{ itemProps, opt }">
                <q-item v-bind="itemProps">
                  <q-item-section>
                    <div>
                      <q-chip
                          class="truncate-chip-labels"
                          outline
                          :style="!!opt.IsTagUsedInThisItem ? { 'background-color': '#ffffff', 'color': '#7f7f7f', 'width': 'auto', 'margin-left': opt.LeftPadding } : { 'background-color': opt.ColorCode, 'color': opt.TextCode, 'width': 'auto', 'margin-left': opt.LeftPadding }">
                        <q-icon class="ChipIcon" :style="{ 'color': (!!opt.IsTagUsedInThisItem ? '#7f7f7f' : opt.TextCode) }" :name="opt.IconCode" />
                        {{ opt.TagName }}
                      </q-chip>
                    </div>
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <q-input v-model="Item.ItemCost"
                     type="number"
                     prefix="$"
                     label="Cost"
                     @change="FormatCost" />
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="ControlLabel">Description:</div>
            <q-editor v-model="Item.ItemDescription" min-height="5rem" />
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" icon="save" @click="SaveItemClick()" />
        <q-btn class="glossy" rounded color="negative" icon="delete_forever" @click="DeleteItemClick()" v-if="!NewItemMode" />
      </q-card-actions>
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

    <q-dialog v-model="ShowDeleteDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6">Delete Item</div>
          </div>
        </q-card-section>
        <q-card-section>
          Are you sure that you want to delete this item?
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="cancel" v-close-popup />
          <q-btn class="glossy" rounded color="negative" label="Delete" @click="DeleteItemConfirmClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowDeletePhotoDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6">Delete Photo</div>
          </div>
        </q-card-section>
        <q-card-section>
          Are you sure you want to delete this photo?
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="Cancel" v-close-popup />
          <q-btn class="glossy" rounded color="negative" label="Delete" @click="DeletePhotoConfirmClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowNewPhotoDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6">Add Photo</div>
          </div>
        </q-card-section>
        <q-card-section>
          <div id="PhotoInputParent">
            <q-file
                style="max-width: 300px"
                v-model="NewPhotoModel"
                filled
                rounded
                label="Photo"
                accept="image/*"
                max-file-size="5120000"
                @input="PhotoSelected"
              >
              <template v-slot:prepend>
                <q-icon name="photo_camera" />
              </template>
            </q-file>
          </div>
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="Cancel" v-close-popup />
          <q-btn class="glossy" rounded color="primary" label="Save" @click="SaveNewPhotoConfirmClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowLargePhotoDialog">
      <q-card class="InfoCard col-auto LargePhotoCard">
        <div class="row">
          <div class="col-auto">
            <img :src="SelectedPhoto.PhotoData" class="LargePhoto" />
          </div>
        </div>
        <div class="row">
          <div class="col-auto">
            <q-btn class="glossy" rounded color="negative" icon="delete_forever" @click="DeletePhotoClick()" />
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
import notification from "../utils/notification";


export default {
  name: "PageItem",

  data()
  {
    return {
      Item: {
        ItemID: "",
        ItemName: "",
        ItemDescription: "",
        ItemCost: 0,
      },
      Locations: [],
      Tags: [],
      TagNodes: [],
      TagsOrdered: [],
      NewTag: {
        TagID: "", 
        TagName: "Add a tag...",
      },
      SelectedPhoto: {},
      NewItemMode: false,
      ShowDeleteDialog: false,
      ShowDeletePhotoDialog: false,
      ShowNewPhotoDialog: false,
      ShowLargePhotoDialog: false,
      NewPhotoModel: null,
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
    this.LoadLocations();
  },

  methods:
  {
    CheckProperties: function()
    {
      if (!this.$store.getters["user/IsLoggedIn"])
      {
        this.$router.push("/welcome");
      }
      else
      {
        if (this.ItemID.toLocaleLowerCase() === "new")
        {
          this.NewItemMode = true;
          this.PageLoaded = true;
        }
        else if (this.ItemID.length > 10)
        {
          this.LoadItem();
        }
        else
        {
          this.$route.push("/home");
        }
      }
    },

    LoadItem: function()
    {
      api.get("item/one/" + this.ItemID, this.$store)
          .then((response) =>
          {
            this.Item = response.Item;

            this.LoadTags().then(() => {
              this.NewItemMode = false;
              this.PageLoaded = true;
            });
          }).catch((e) =>
          {
            error.HandleError("Get item error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
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
            this.FlagUsedTags();
            this.MakeTagNodes();
            this.OrderTags();
          });
    },

    FlagUsedTags: function()
    {
      this.Tags.forEach(t => {
        var MatchingTags = this.Item.Tags.filter(m => { return (m.TagID === t.TagID); });

        t.IsTagUsedInThisItem = (MatchingTags.length > 0) ? 1 : 0;
      });
    },

    SaveItemClick: function()
    {
      if (this.Item.ItemName.length < 1)
      {
        notification.ShowFailure("Enter an item name.");
      }
      else
      {
        if (this.NewItemMode)
        {
          api.post("item/add", { Item: this.Item }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The item was added.");
                this.$router.push("/home");
              }).catch((e) =>
              {
                error.HandleError("Add item error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
        else
        {
          api.post("item/update", { Item: this.Item }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The item was saved.");
              }).catch((e) =>
              {
                error.HandleError("Update item error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
      }
    },

    DeleteItemClick: function()
    {
      this.ShowDeleteDialog = true;
    },

    DeleteItemConfirmClick: function()
    {
      api.get("item/delete/" + this.ItemID, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The item was deleted.");
                this.ShowDeleteDialog = false;
                this.$router.push("/home");
              }).catch((e) =>
              {
                error.HandleError("Delete item error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
    },

    DeletePhotoClick: function()
    {
      this.ShowDeletePhotoDialog = true;
    },

    DeletePhotoConfirmClick: function()
    {
      api.get("photo/delete/" + this.SelectedPhoto.PhotoID, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The photo was deleted.");
                this.ShowDeletePhotoDialog = false;
                this.ShowLargePhotoDialog = false;
                this.CheckProperties();
              }).catch((e) =>
              {
                error.HandleError("Delete photo error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
    },

    SaveNewPhotoClick: function()
    {
      this.SelectedPhoto = {
        PhotoID: "",
        PhotoData: "",
        ItemID: this.ItemID,
      };

      this.NewPhotoModel = null;
      this.ShowNewPhotoDialog = true;
    },

    SaveNewPhotoConfirmClick: function()
    {
      if (this.SelectedPhoto.PhotoData.length < 2)
      {
        notification.ShowFailure("Select a photo.");
      }
      else
      {
        api.post("photo/add", { Photo: this.SelectedPhoto }, this.$store)
            .then((response) =>
            {
              this.ShowNewPhotoDialog = false;
              notification.ShowSuccess("The photo was added.");
              this.CheckProperties();
            }).catch((e) =>
            {
              error.HandleError("Add photo error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
            });
      }
    },

    PhotoSelected: function()
    {
      var PhotoInputParent = document.getElementById("PhotoInputParent");
      var PhotoInputs = PhotoInputParent.getElementsByTagName("input");
      var PhotoInput = PhotoInputs[0];
      var PhotoFiles = PhotoInput.files;

      if (PhotoFiles.length > 0)
      {
        var PhotoFile = PhotoFiles[0];

        this.SelectedPhoto.PhotoData = "";
        const PhotoReader = new FileReader();
        PhotoReader.addEventListener("load", (event) => {
          this.SelectedPhoto.PhotoData += event.target.result;
        });
        PhotoReader.readAsDataURL(PhotoFile);
      }
      else
      {
        //  Clear stuff.
        this.ReportPhotoModel = null;
        this.SelectedPhoto.PhotoData = "";
      }
    },

    ShowLargePhoto: function(Photo)
    {
      this.SelectedPhoto = Photo;
      this.ShowLargePhotoDialog = true;
    },

    FormatCost: function()
    {
      var Output = "0";
      
      try
      {
        var Cost = numeral(this.Item.ItemCost);
        Output = Cost.format("0,0.00");
      }
      catch
      {}

      this.Item.ItemCost = Output;
    },

    SelectNewChip: function()
    {
      if (!this.NewTag.IsTagUsedInThisItem)
      {
        //  Flag the tag as having been used.
        var MatchingTags = this.Tags.filter(t => { return (t.TagID.toString() === this.NewTag.TagID.toString()); } );
        MatchingTags.forEach(t => { t.IsTagUsedInThisItem = 1; });

        //  Add the new tag.
        this.Item.Tags.push(this.NewTag);
      }

      this.NewTag = {
        TagID: "",
        TagName: "Add a tag...",
      };
    },

    RemoveChip: function(Tag)
    {
      var IndexOfTagToRemove = this.Item.Tags.indexOf(Tag);

      //  Remove "is used" flag so that it shows up in the select list.
      var MatchingTags = this.Tags.filter(t => { return (t.TagID.toString() === Tag.TagID.toString()); } );
      MatchingTags.forEach(t => { t.IsTagUsedInThisItem = 0; });

      //  Remove the tag from the list of tags in this note.
      if (IndexOfTagToRemove >= 0)
      {
        this.Item.Tags.splice(IndexOfTagToRemove, 1);
      }
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
