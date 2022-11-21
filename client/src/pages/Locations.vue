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
        An item could be in your bedroom, the park, or a country.
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
              <div class="row items-center LocationEntry" :style="{ 'background-color': t.ColorCode, 'color': t.TextCode }">
                <q-icon :name="t.IconCode" class="LocationIcon" size="xl" />
                <div>{{ t.LocationName }}</div>
              </div>
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
            <div class="col text-h6" v-if="SelectedLocation.LocationID.length < 1">Add a Location</div>
            <div class="col text-h6" v-if="SelectedLocation.LocationID.length > 0">Edit a Location</div>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-md-12">
              <q-input v-model="SelectedLocation.LocationName" label="Location Name" />
            </div>
          </div>
          <div class="row FormRow">
            <div class="col-md-12">
              <q-select
                v-model="SelectedIcon"
                :options="Icons"
                option-value="IconID"
                option-label="IconName"
                label="Icon">
                <template v-slot:option="{ itemProps, opt }">
                  <q-item v-bind="itemProps">
                    <q-item-section avatar>
                      <q-icon :name="opt.IconCode" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ opt.IconName }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
            </div>
          </div>
          <div class="row FormRow">
            <div class="col-md-12">
              <q-select
                v-model="SelectedColor"
                :options="Colors"
                option-value="ColorID"
                option-label="ColorName"
                label="Color">
                <template v-slot:option="{ itemProps, opt }">
                  <q-item v-bind="itemProps">
                    <q-item-section avatar>
                      <q-icon name="tag_faces" :style="{ 'color': opt.TextCode, 'background-color': opt.ColorCode, 'padding': '8px' }" />
                    </q-item-section>
                    <q-item-section>
                      <q-item-label>{{ opt.ColorName }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
              </q-select>
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
      Colors: [],
      Icons: [],
      ShowEditDialog: false,
      ShowDeleteDialog: false,
      SelectedLocation: {},
      SelectedColor: {},
      SelectedIcon: {},
    };
  },

  mounted()
  {
    this.LoadLocations();
    this.LoadIcons();
    this.LoadColors();
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

    LoadIcons: function()
    {
      api.get("icon", this.$store)
          .then((response) =>
          {
            this.Icons = response.Icons;
          }).catch((e) =>
          {
            error.HandleError("Get icons error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },

    LoadColors: function()
    {
      api.get("color", this.$store)
          .then((response) =>
          {
            this.Colors = response.Colors;
          }).catch((e) =>
          {
            error.HandleError("Get colors error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
          });
    },

    EditLocationClick: function(Location)
    {
      this.SelectedLocation = Location;
      var MatchingIcons = this.Icons.filter(i => { return (i.IconID === this.SelectedLocation.IconID); });

      if (MatchingIcons.length > 0)
      {
        this.SelectedIcon = MatchingIcons[0];
      }
      else
      {
        this.SelectedIcon = this.Icons[0];
      }
      

      var MatchingColors = this.Colors.filter(c => { return (c.ColorID === this.SelectedLocation.ColorID); });

      if (MatchingColors.length > 0)
      {
        this.SelectedColor = MatchingColors[0];
      }
      else
      {
        this.SelectedColor = this.Colors[0];
      }

      this.ShowEditDialog = true;
    },

    NewLocationClick: function()
    {
      this.SelectedLocation = {
        LocationID: "",
        LocationName: "",
      };

      this.SelectedColor = {
        ColorID: "",
        ColorName: "- NONE -",
      };

      this.SelectedIcon = {
        IconID: "",
        IconName: "- NONE -",
      };

      this.ShowEditDialog = true;
    },

    SaveLocationClick: function()
    {
      if (this.SelectedLocation.LocationName.length < 1)
      {
        notification.ShowFailure("Enter a Location name.");
      }
      else
      {
        this.SelectedLocation.IconID = this.SelectedIcon.IconID;
        this.SelectedLocation.ColorID = this.SelectedColor.ColorID;

        if (this.SelectedLocation.LocationID.length < 1)
        {
          api.post("location/add", { Location: this.SelectedLocation }, this.$store)
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
          api.post("location/update", { Location: this.SelectedLocation }, this.$store)
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
      this.SelectedLocation = Tag;
      this.ShowDeleteDialog = true;
    },

    DeleteLocationConfirmClick: function()
    {
      api.get("location/delete/" + this.SelectedLocation.LocationID, this.$store)
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

<style scoped>

div.LocationEntry
{
  padding: 8px;
  margin-left: 16px;
}

i.LocationIcon
{
  padding-right: 8px;
}

</style>
