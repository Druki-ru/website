<template>
  <div class="search">
    <div class="search__input-pane">
      <input type="text"
             placeholder="Давайте попробуем что-нибудь найти"
             class="form-control search__input"
             name="text"
             autocomplete="off"
             v-model="text"
             @focus="onFocus"
             @blur="onBlur">
      <span class="search__input-icon"></span>
    </div>

    <div class="search__results-pane">
      <div class="search__results-content">
        <div class="search__results">
          <div v-show="!result.items">
            Ничего не найдено
          </div>
          <div class="search__result" v-for="item in result.items">
            <a :href="item.url" class="search__result-link">
              <h2 class="search__result-title">{{ item.label }}</h2>
              <div class="search__result-url">{{ item.url }}</div>
            </a>
            <div class="search__result-core" v-if="item.core">
              Drupal {{ item.core }}
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
              <div class="search__filter-item-label">
                <label for="search-difficulty-1">Не указана</label>
              </div>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-2"
                     type="checkbox"
                     name="difficulty"
                     value="basic"
                     v-model="filterDifficulty">
              <div class="search__filter-item-label">
                <label for="search-difficulty-2">Базовая</label>
                <div class="search__filter-item-description">
                  Подойдет новичкам без каких-либо навыков.
                </div>
              </div>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-3"
                     type="checkbox"
                     name="difficulty"
                     value="medium"
                     v-model="filterDifficulty">
              <div class="search__filter-item-label">
                <label for="search-difficulty-3">Средняя</label>
                <div class="search__filter-item-description">
                  Потребуются базовые знания и знакомство с административным
                  интерфейсом.
                </div>
              </div>
            </div>

            <div class="search__filter-item">
              <input id="search-difficulty-4"
                     type="checkbox"
                     name="difficulty"
                     value="advanced"
                     v-model="filterDifficulty">
              <div class="search__filter-item-label">
                <label for="search-difficulty-4">Продвинутая</label>
                <div class="search__filter-item-description">
                  Потребуется написание кода.
                </div>
              </div>
            </div>
          </div>

          <div class="search__filter">
            <div class="search__filter-label">Версия ядра</div>

            <div class="search__filter-item">
              <input id="search-core-1"
                     type="checkbox"
                     name="core"
                     value="none"
                     v-model="filterCore">

              <div class="search__filter-item-label">
                <label for="search-core-1">Не указана</label>
              </div>
            </div>

            <div class="search__filter-item">
              <input id="search-core-2"
                     type="checkbox"
                     name="core"
                     value="8"
                     v-model="filterCore">

              <div class="search__filter-item-label">
                <label for="search-core-2">Drupal 8</label>
              </div>
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
  // @todo Maybe there is a good reason to split up all this markup to separate
  // components.
  import Drupal from 'drupal';

  export default {
    name: 'Search',

    data: () => ({
      text: '',
      filterDifficulty: [],
      filterCore: [],
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

      filterDifficulty: function() {
        this.doSearch();
      },

      filterCore: function() {
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
        if (this.text.length >= 3 || this.filterDifficulty.length || this.filterCore.length) {
          let params = {};

          if (this.text.length >= 3) {
            params = Object.assign(params, {
              text: this.text,
            })
          }

          if (this.filterDifficulty.length) {
            params = Object.assign(params, {
              difficulty: this.filterDifficulty,
            });
          }

          if (this.filterCore.length) {
            params = Object.assign(params, {
              core: this.filterCore,
            });
          }

          this.$store.dispatch('SEARCH_REQUEST', params);
          this.updateCurrentUrl();
        }
      }, 300),

      updateCurrentUrl() {
        const params = new URLSearchParams(location.search);

        if (this.text.length >= 3) {
          params.set('text', this.text);
        }
        else {
          params.delete('text');
        }

        if (this.filterDifficulty.length) {
          params.set('difficulty', this.filterDifficulty);
        }
        else {
          params.delete('difficulty');
        }

        if (this.filterCore.length) {
          params.set('core', this.filterCore);
        }
        else {
          params.delete('core');
        }

        window.history.replaceState({}, '', `${location.pathname}?${params}`);
      },
    },

    created() {
      const params = new URLSearchParams(location.search);
      if (Array.from(params).length) {
        if (params.has('text') && params.get('text').length) {
          this.text = params.get('text');
        }

        if (params.has('core') && params.get('core').length) {
          this.filterCore = params.get('core').split(',');
        }

        if (params.has('difficulty') && params.get('difficulty').length) {
          this.filterDifficulty = params.get('difficulty').split(',');
        }

        this.doSearch();
      }
    },
  };
</script>
