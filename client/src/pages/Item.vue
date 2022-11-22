<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto ItemCard">
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
            <q-select
                v-model="Item.Tags"
                multiple
                :options="Tags"
                option-value="TagID"
                option-label="TagName"
                label="Tags">
                <template v-slot:option="{ itemProps, opt }">
                  <q-item v-bind="itemProps">
                    <q-item-section avatar :style="{ 'background-color': opt.ColorCode, 'color': opt.TextCode }">
                      <q-icon :name="opt.IconCode" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ opt.TagName }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
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
                capture="camera"
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
      },
      Locations: [],
      Tags: [],
      SelectedPhoto: {},
      NewItemMode: false,
      ShowDeleteDialog: false,
      ShowDeletePhotoDialog: false,
      ShowNewPhotoDialog: false,
      ShowLargePhotoDialog: false,
      NewPhotoModel: null,
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
      this.CheckProperties();
    },
  },

  mounted()
  {
    this.CheckProperties();
    this.LoadLocations();
    this.LoadTags();
  },

  methods:
  {
    CheckProperties: function()
    {
      if (this.ItemID.toLocaleLowerCase() === "new")
      {
        this.NewItemMode = true;
      }
      else if (this.ItemID.length > 10)
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
      api.get("item/one/" + this.ItemID, this.$store)
          .then((response) =>
          {
            this.Item = response.Item;
            this.NewItemMode = false;
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
      api.get("tag", this.$store)
          .then((response) =>
          {
            this.Tags = response.Tags;
          }).catch((e) =>
          {
            error.HandleError("Get tags error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
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
  },
};

</script>

<style scoped>

.ItemCard
{
  min-width: 40%;
}

i.LocationIcon
{
  padding-right: 8px;
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
