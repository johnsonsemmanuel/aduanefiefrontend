import request from './request';

const deliveryService = {
  get: (params) => request.get('dashboard/admin/users/paginate', { params }),
  getAll: (params) =>
    request.get('dashboard/admin/couriers/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/admin/courier-settings/${id}`, { params }),
  create: (data) =>
    request.post('dashboard/admin/courier-settings', data, {}),
  update: (id, data) =>
    request.put(`dashboard/admin/courier-settings/${id}`, data, {}),
  getTypes: (params) =>
    request.get(`dashboard/admin/delivery/types`, { params }),
  delete: (params) =>
    request.delete(`dashboard/admin/users/delete`, { params }),
  dropAll: () => request.get(`dashboard/admin/couriers/drop/all`),
  restoreAll: () => request.get(`dashboard/admin/couriers/restore/all`),
};

export default deliveryService;
