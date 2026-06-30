import request from '../request';

const orderService = {
  getAll: (params) =>
    request.get('dashboard/market.attendant/orders/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/market.attendant/orders/${id}`, { params }),
  create: (data) => request.post('dashboard/market.attendant/orders', data),
  update: (id, data) => request.put(`dashboard/market.attendant/orders/${id}`, data),
  updateStatus: (id, data) =>
    request.post(`dashboard/market.attendant/order/${id}/status/update`, data),
  updateDelivery: (id, data) =>
    request.post(`dashboard/market.attendant/orders/order/${id}/courier`, data),
  delete: (params) =>
    request.delete(`dashboard/market.attendant/orders/orders/delete`, {
      params,
    }),
  attachToMe: (id) => request.post(`dashboard/market.attendant/order/${id}/attach/me`),
};

export default orderService;
