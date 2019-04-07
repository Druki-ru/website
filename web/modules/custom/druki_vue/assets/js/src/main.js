/**
 * Some help links:
 * - https://medium.com/vuetify/productivity-in-vue-part-3-697d6407498e
 */
import Drupal from 'drupal';
import store from './store';
import Search from './components/scoped/Search.vue';

/**
 * Behavior for attaching all vue components when page is updates.
 */
Drupal.behaviors.drukiVueInit = {
  attach: function(context) {
    this.attachSearch(context);
  },

  /**
   * Attaches header search component.
   */
  attachSearch: function(context) {
    let searchElements = context.querySelectorAll('.search-init');

    if (searchElements.length) {
      searchElements.forEach(element => {
        new Vue({
          render: h => h(Search),
          store,
        }).$mount(element);
      });
    }

  },
};
