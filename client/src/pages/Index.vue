<template>
  <q-page v-masonry trsnsition-dureciton="0.3s" gutter="10" stagger="0.03s">
    <q-card v-masonry-tile class="InfoCard col-auto" v-if="!$store.getters['user/IsLoggedIn']">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Who are you?</div>
        </div>
      </q-card-section>
      <q-card-section>
        You should log in.
      </q-card-section>
    </q-card>

    <q-card v-masonry-tile class="InfoCard col-auto" v-if="$store.getters['user/IsLoggedIn']">
      <q-card-section>
        <div class="row">
          <div class="col text-h6">Ahhh!  Hello, {{ $store.getters["user/GetUser"].UserName }}</div>
        </div>
      </q-card-section>
      <q-card-section>
        What up.
      </q-card-section>
    </q-card>
  </q-page>
</template>

<script>

export default {
  name: "PageIndex",
};

</script>
