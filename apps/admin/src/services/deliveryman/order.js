import request from '../request';

const orderService = {
  getAll: (params) =>
    request.get('dashboard/courier/orders/paginate', { params }),
  getById: (id, params) =>
    request.get(`dashboard/courier/orders/${id}`, { params }),
  updateStatus: (id, data) =>
    request.post(
      `dashboard/courier/order/details/${id}/status/update`,
      data
    ),
};

export default orderService;
