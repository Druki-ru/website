/**
 * Some help links:
 * - https://medium.com/vuetify/productivity-in-vue-part-3-697d6407498e
 */
import Drupal from 'drupal';
import store from './store';
import HeaderSearch from './components/scoped/HeaderSearch.vue';

/**
 * Behavior for attaching all vue components when page is updates.
 */
Drupal.behaviors.drukiVueInit = {
  attach: function() {
    this.attachHeaderSearch();
  },

  /**
   * Attaches header search component.
   */
  attachHeaderSearch: function() {
    new Vue({
      render: h => h(HeaderSearch),
      store,
    }).$mount('.header-search-init');
  },
};
