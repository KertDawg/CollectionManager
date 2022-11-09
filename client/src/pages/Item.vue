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
    </q-card>
  </q-page>
</template>

<script>

export default {
  name: "PageItem",

  data()
  {
    return {
      Item: {
        ItemID: "",
        ItemName: "",        
      },
    };
  },

  props:  
  {
    ItemID: {
      type: String,
      default: "-1",
    },
  },
};

</script>
