import request from '../request';
const paymentFromPartnerService = {
  getAll: (params) =>
    request.get('dashboard/courier/payment-to-partners', { params }),
};

export default paymentFromPartnerService;
