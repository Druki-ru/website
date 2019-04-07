const resource = '/api/search';
const headers = {
  'Content-Type': 'application/json',
};

export default {
  doGlobalSearch(text) {
    return fetch(`${resource}/global?text=${text}`, {
      'method': 'GET',
      headers,
    });
  },
};
