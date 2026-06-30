import request from './request';

const installationService = {
  checkInitFile: (params) => request.get('install/init/check', { params }),
  setInitFile: (data) => request.post('install/init/set', data),
  updateDatabase: (data) => request.post(`install/database/update`, data),
  migrationRun: (data) => request.post('install/migration/run', data),
  createAdmin: (data) => request.post(`install/admin/create`, data),
  createLang: (data) => request.post(`install/languages/create`, data),
};

export default installationService;
