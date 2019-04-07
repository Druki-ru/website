<template>
  <div class="search">
    <div class="search__input-pane">
      <input type="text"
             placeholder="Давайте попробуем что-нибудь найти"
             class="form-control search__input"
             name="text"
             v-model="text"
             @focus="onFocus"
             @blur="onBlur">
      <span class="search__input-icon"></span>
    </div>

    <div class="search__results-pane">
      <div class="search__results-content">
        <div class="search__results">
          Результаты поиска

          <div v-for="item in result.items">
            <div class="search__result">
              <h2 class="search__result-title">{{ item.label }}</h2>
              <div class="search__result-url">{{ item.url }}</div>
              <div class="search__result-core" v-if="item.core">
                Drupal {{ item.core }}
              </div>
              <a :href="item.url" class="search__result-link"></a>
            </div>
          </div>
        </div>

        <div class="search__filters">
          <div class="search__filter">
            <div class="search__filter-label">Сложность</div>

            <div class="search__filter-item">
              <input id="search-difficulty-1"
                     type="checkbox"
                     name="difficulty"
                     value="none"
                     v-model="filterDifficulty">
              <label for="search-difficulty-1">Не указана</label>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-2"
                     type="checkbox"
                     name="difficulty"
                     value="basic"
                     v-model="filterDifficulty">
              <label for="search-difficulty-2">Базовая</label>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-3"
                     type="checkbox"
                     name="difficulty"
                     value="medium"
                     v-model="filterDifficulty">
              <label for="search-difficulty-3">Средняя</label>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-4"
                     type="checkbox"
                     name="difficulty"
                     value="advanced"
                     v-model="filterDifficulty">
              <label for="search-difficulty-4">Продвинутая</label>
            </div>
          </div>
        </div>
      </div>

      <div class="search__loading" v-show="status === 'loading'">
        <svg
          width="48"
          height="48"
          viewBox="0 0 680 666">
          <use xlink:href="#druki-loading-svg"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script>
  import Drupal from 'drupal';

  export default {
    name: 'Search',

    data: () => ({
      text: '',
      filterDifficulty: [],
    }),

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

    watch: {
      text: function() {
        this.doSearch();
      },
    },

    methods: {
      onFocus() {
        this.focused = true;
      },
      onBlur() {
        this.focused = false;
      },
      doSearch: Drupal.debounce(function() {
        if (this.text.length >= 3) {
          this.$store.dispatch('SEARCH_REQUEST', this.text);
          this.updateCurrentUrl();
        }
      }, 300),
      updateCurrentUrl() {
        const params = new URLSearchParams(location.search);
        params.set('text', this.text);

        window.history.replaceState({}, '', `${location.pathname}?${params}`);
      },
    },

    created() {
      const params = new URLSearchParams(location.search);
      if (Array.from(params).length && params.get('text').length) {
        this.text = params.get('text');
      }
    },
  };
</script>
