import qs from 'qs';

const resource = '/api/search';
const headers = {
  'Content-Type': 'application/json',
};

export default {
  doGlobalSearch(params) {
    let query = qs.stringify(params);

    console.log(query);
    return fetch(`${resource}/global?${query}`, {
      'method': 'GET',
      headers,
    });
  },
};
