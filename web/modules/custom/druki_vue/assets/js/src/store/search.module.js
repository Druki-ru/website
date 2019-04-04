const state = {
  result: [],
  status: '',
};

const getters = {
  searchResult: state => state.result,
  searchStatus: state => state.status,
};

const actions = {
  SEARCH_REQUEST: ({commit}, text) => {
    return new Promise((resolve, reject) => {
      commit('SEARCH_REQUEST');
      fetch('https://jsonplaceholder.typicode.com/todos/1')
        .then(response => response.json())
        .then(json => {
          commit('SEARCH_SUCCESS', json);
          resolve(json);
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
