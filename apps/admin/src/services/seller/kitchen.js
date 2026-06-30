import request from '../request';

const url = 'dashboard/seller/packhouse';

const packhouseService = {
  getAll: (params) => request.get(url, { params }),
  getById: (id, params) => request.get(`${url}/${id}`, { params }),
  create: (data) => request.post(url, data),
  update: (id, data) => request.put(`${url}/${id}`, data),
  delete: (params) => request.delete(`${url}/delete`, { params }),
};

export default packhouseService;
