<template>
  <q-page v-masonry transition-duration="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Tags</div>
        </div>
      </q-card-section>
      <q-card-section>
        Edit your tags here.
        Tags can be added to items to group them.
        A item could be a car, clothing, a pen, or another type of object.
        These groups would be tags.
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" label="Add a New Tag" @click="NewTagClick" />
      </q-card-actions>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <q-tree
          :nodes="TagNodes"
          default-expand-all
          node-key="TagID"
          children-key="Children"
          ref="TagTree"
          :duration="0">
          <template v-slot:default-header="prop">
            <div @click="ExpandTree">
              <q-btn flat dense icon="add" @click="NewTagClick(prop.node)" />
              <q-btn flat dense icon="edit" @click="EditTagClick(prop.node)" />
              <q-btn flat dense icon="delete" @click="DeleteTagClick(prop.node)" />
            </div>
            <div class="row items-center TagTreeNode" :style="{ 'background-color': prop.node.ColorCode, 'color': prop.node.TextCode }" @click="ExpandTree">
              <q-icon :name="prop.node.IconCode" class="TagTreeIcon" />
              <div>{{ prop.node.TagName }}</div>
            </div>
          </template>
        </q-tree>
      </q-card-section>
    </q-card>

    <q-dialog v-model="ShowEditDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6" v-if="SelectedTag.TagID.length < 1">Add a Tag</div>
            <div class="col text-h6" v-if="SelectedTag.TagID.length > 0">Edit a Tag</div>
          </div>
        </q-card-section>
        <q-card-section>
          <div class="row">
            <div class="col-md-12">
              <q-input v-model="SelectedTag.TagName" label="Tag Name" />
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
          <q-btn class="glossy" rounded color="primary" label="Save" @click="SaveTagClick" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <q-dialog v-model="ShowDeleteDialog">
      <q-card v-masonry-tile class="InfoCard col-auto">
        <q-card-section>
          <div class="row">
            <div class="col text-h6">Delete a Tag</div>
          </div>
        </q-card-section>
        <q-card-section>
          Are you sure that you want to delete this tag?
        </q-card-section>
        <q-card-actions>
          <q-btn class="glossy" rounded label="cancel" v-close-popup />
          <q-btn class="glossy" rounded color="negative" label="Delete" @click="DeleteTagConfirmClick" />
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
      TagNodes: [],
      Colors: [],
      Icons: [],
      ShowEditDialog: false,
      ShowDeleteDialog: false,
      SelectedTag: {},
      SelectedColor: {},
      SelectedIcon: {},
    };
  },

  mounted()
  {
    this.LoadTags();
    this.LoadIcons();
    this.LoadColors();
  },

  methods:
  {
    LoadTags: function()
    {
      api.get("tag", this.$store)
          .then((response) =>
          {
            this.Tags = response.Tags;
            this.MakeTagNodes();
            this.ExpandTree();
          }).catch((e) =>
          {
            error.HandleError("Get tags error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
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

    EditTagClick: function(Tag)
    {
      this.SelectedTag = Tag;
      var MatchingIcons = this.Icons.filter(i => { return (i.IconID === this.SelectedTag.IconID); });

      if (MatchingIcons.length > 0)
      {
        this.SelectedIcon = MatchingIcons[0];
      }
      else
      {
        this.SelectedIcon = this.Icons[0];
      }
      

      var MatchingColors = this.Colors.filter(c => { return (c.ColorID === this.SelectedTag.ColorID); });

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

    NewTagClick: function(ParentTag)
    {
      this.SelectedTag = {
        TagID: "",
        TagName: "",
        ParentTagID: "",
      };

      if (typeof ParentTag !== "undefined")
      {
        this.SelectedTag.ParentTagID = ParentTag.TagID;
      }

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

    SaveTagClick: function()
    {
      if (this.SelectedTag.TagName.length < 1)
      {
        notification.ShowFailure("Enter a tag name.");
      }
      else
      {
        this.SelectedTag.IconID = this.SelectedIcon.IconID;
        this.SelectedTag.ColorID = this.SelectedColor.ColorID;

        if (this.SelectedTag.TagID.length < 1)
        {
          api.post("tag/add", { Tag: this.SelectedTag }, this.$store)
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
          api.post("tag/update", { Tag: this.SelectedTag }, this.$store)
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

    DeleteTagClick: function(Tag)
    {
      this.SelectedTag = Tag;
      this.ShowDeleteDialog = true;
    },

    DeleteTagConfirmClick: function()
    {
      api.get("tag/delete/" + this.SelectedTag.TagID, this.$store)
              .then((response) =>
              {
                notification.ShowSuccess("The tag was deleted.");
                this.LoadTags();
                this.ShowDeleteDialog = false;
              }).catch((e) =>
              {
                error.HandleError("Delete tag error: " + JSON.stringify(e), error.ERROR_LEVEL_FATAL);
              });
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

    ExpandTree: function()
    {
      setTimeout(() => { this.$refs.TagTree.expandAll(); }, 10);
    },
  },
};

</script>

<style scoped>

div.TagTreeNode
{
  padding: 8px;
  margin-left: 16px;

}

i.TagTreeIcon
{
  padding-right: 8px;
}

</style>
