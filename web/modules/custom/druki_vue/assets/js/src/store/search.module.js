import SearchRepository from '../repositories/search.repository';

const state = {
  result: [],
  status: '',
};

const getters = {
  searchResult: state => state.result,
  searchStatus: state => state.status,
};

const actions = {
  SEARCH_REQUEST: ({commit}, params) => {
    return new Promise((resolve, reject) => {
      commit('SEARCH_REQUEST');
      SearchRepository
        .doGlobalSearch(params)
        .then(result => result.json())
        .then(result => {
          commit('SEARCH_SUCCESS', result);
          resolve(result);
        })
        .catch(error => {
          commit('SEARCH_ERROR', error);
          reject(error);
        });
    });
  }
};

const mutations = {
  SEARCH_REQUEST: (state) => {
    state.status = 'loading';
  },
  SEARCH_SUCCESS: (state, result) => {
    state.status = 'success';
    state.result = result;
  },
  SEARCH_ERROR: (state, error) => {
    state.status = 'error';
    console.log('Search Error: ' + error);
  },
};

export default {
  state,
  actions,
  getters,
  mutations,
}
