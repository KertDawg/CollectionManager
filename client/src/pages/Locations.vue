<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" sLocationger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Locations</div>
        </div>
      </q-card-section>
      <q-card-section>
        Edit your Locations here.
        Locations can be added to items to group them.
        An item could be a car, clothing, a pen, or another type of object.
        These would be Locations.
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" label="Add a New Location" @click="NewLocationClick" />
      </q-card-actions>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <q-list bordered>
          <q-item v-for="t in Locations" :key="t.LocationID">
            <q-item-section>
              {{ t.LocationName }}
            </q-item-section>
            <q-item-section side>
              <q-btn class="glossy" rounded icon="edit" @click="EditLocationClick(t)" />
            </q-item-section>
            <q-item-section side>
              <q-btn class="glossy" rounded color="negative" icon="delete_forever" @click="DeleteLocationClick(t)" />
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>

    <q-dialog v-model="ShowEditDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6" v-if="LocationToEdit.LocationID.length < 1">Add a Location</div>
            <div class="col text-h6" v-if="LocationToEdit.LocationID.length > 0">Edit a Location</div>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-md-12">
              <q-input v-model="LocationToEdit.LocationName" label="Location Name" />
            </div>
          </div>
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="cancel" v-close-popup />
          <q-btn class="glossy" rounded color="primary" label="Save" @click="SaveLocationClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowDeleteDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6">Delete a Location</div>
          </div>
        </q-card-section>
        <q-card-section>
          Are you sure that you want to delete this location?
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="cancel" v-close-popup />
          <q-btn class="glossy" rounded color="negative" label="Delete" @click="DeleteLocationConfirmClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>
   </q-page>
</template>

<script>

import api from "../utils/api";
import error from "../utils/error";
import notification from "../utils/notification";


export default {
  name: "PageLocations",

  data()
  {
    return {
      Locations: [],
      ShowEditDialog: false,
      ShowDeleteDialog: false,
      LocationToEdit: {},
    };
  },

  mounted()
  {
    this.LoadLocations();
  },

  methods:
  {
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

    EditLocationClick: function(Location)
    {
      this.LocationToEdit = Location;
      this.ShowEditDialog = true;
    },

    NewLocationClick: function()
    {
      this.LocationToEdit = {
        LocationID: "",
        LocationName: "",
      };

      this.ShowEditDialog = true;
    },

    SaveLocationClick: function()
    {
      if (this.LocationToEdit.LocationName.length < 1)
      {
        notification.ShowFailure("Enter a Location name.");
      }
      else
      {
        if (this.LocationToEdit.LocationID.length < 1)
        {
          api.post("location/add", { Location: this.LocationToEdit }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The location was added.");
                this.LoadLocations();
                this.ShowEditDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Add location error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
        else
        {
          api.post("location/update", { Location: this.LocationToEdit }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The location was saved.");
                this.LoadLocations();
                this.ShowEditDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Update location error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
      }
    },

    DeleteLocationClick: function(Tag)
    {
      this.TagToEdit = Tag;
      this.ShowDeleteDialog = true;
    },

    DeleteLocationConfirmClick: function()
    {
      api.get("location/delete/" + this.LocationToEdit.LocationID, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The location was deleted.");
                this.LoadLocations();
                this.ShowDeleteDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Delete location error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
    },
  },
};

</script>
