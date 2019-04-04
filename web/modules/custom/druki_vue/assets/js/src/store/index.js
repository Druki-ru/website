import Vue from 'vue';
import Vuex from 'vuex';
import search from './search.module';

Vue.use(Vuex);

export default new Vuex.Store({
  modules: {
    search
  },
});
