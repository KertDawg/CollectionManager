<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Tags</div>
        </div>
      </q-card-section>
      <q-card-section>
        Edit your tags here.
        Tags can be added to items to group them.
        An item could be a car, clothing, a pen, or another type of object.
        These would be tags.
      </q-card-section>
      <q-card-actions>
        <q-btn color="primary" label="Add a New Tag" @click="NewTagClick" />
      </q-card-actions>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <q-list bordered>
          <q-item v-for="t in Tags" :key="t.TagID">
            <q-item-section>
              {{ t.TagName }}
            </q-item-section>
            <q-item-section>
              <q-btn icon="edit" @click="EditTagClick(t)" />
            </q-item-section>
          </q-item>
        </q-list>
      </q-card-section>
    </q-card>

    <q-dialog v-model="ShowEditDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6" v-if="TagToEdit.TagID.length < 1">Add a Tag</div>
            <div class="col text-h6" v-if="TagToEdit.TagID.length > 0">Edit a Tag</div>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-md-12">
              <q-input v-model="TagToEdit.TagName" label="Tag Name" />
            </div>
          </div>
        </q-card-section>
        <q-card-actions>
          <q-btn label="cancel" v-close-popup />
          <q-btn color="primary" label="Save" @click="SaveTagClick" />
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
  name: "PageTags",

  data()
  {
    return {
      Tags: [],
      ShowEditDialog: false,
      TagToEdit: {},
    };
  },

  mounted()
  {
    this.LoadTags();
  },

  methods:
  {
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

    EditTagClick: function(Tag)
    {
      this.TagToEdit = Tag;
      this.ShowEditDialog = true;
    },

    NewTagClick: function()
    {
      this.TagToEdit = {
        TagID: "",
        TagName: "",
      };

      this.ShowEditDialog = true;
    },

    SaveTagClick: function()
    {
      if (this.TagToEdit.TagName.length < 1)
      {
        notification.ShowFailure("Enter a tag name.");
      }
      else
      {
        if (this.TagToEdit.TagID.length < 1)
        {
          api.post("tag/add", { Tag: this.TagToEdit }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The tag was added.");
                this.LoadTags();
                this.ShowEditDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Add tag error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
        else
        {
          api.post("tag/update", { Tag: this.TagToEdit }, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The tag was saved.");
                this.LoadTags();
                this.ShowEditDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Update tag error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
        }
      }
    },
  },
};

</script>
