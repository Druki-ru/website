<template>
  <div class="header-search">
    Search
  </div>
</template>

<script>
  export default {
    name: 'HeaderSearch',
    computed: {

      /**
       * Gets results for search.
       */
      result: function() {
        return this.$store.getters['searchResult'];
      },

      /**
       * Gets current status of the search.
       */
      status: function() {
        return this.$store.getters['searchStatus'];
      },

    },
  };
</script>

<style>
  /* Styles for this component handled via Drupal theme. */
</style>
