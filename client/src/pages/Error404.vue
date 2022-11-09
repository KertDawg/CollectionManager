<template>
  <div class="fullscreen bg-blue text-white text-center q-pa-md flex flex-center">
    Bad URL...
  </div>
</template>

<script>

export default {
  name: "PageError",

  mounted()
  {
    this.$router.push({ name: "home" });
  },
};

</script>
