import SearchRepository from './search.repository';

const repositories = {
  search: SearchRepository,
};

export const RepositoryFactory = {
  get: name => repositories[name],
};
