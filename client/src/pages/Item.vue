<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Item</div>
        </div>
      </q-card-section>
      <q-card-section>
        <div class="row">
          <div class="col-12">
            <q-input v-model="Item.ItemName"
                     label="Item Name" />
          </div>
        </div>
      </q-card-section>
      <q-card-actions>
        <q-btn class="glossy" rounded color="primary" icon="save" @click="SaveItemClick()" />
        <q-btn class="glossy" rounded color="negative" icon="delete_forever" @click="DeleteItemClick()" v-if="!NewItemMode" />
      </q-card-actions>
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
      },
      NewItemMode: false,
      ShowDeleteDialog: false,
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
                this.$router.push("/home");
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
  },
};

</script>
