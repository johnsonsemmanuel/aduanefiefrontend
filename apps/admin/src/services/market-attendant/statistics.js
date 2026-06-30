import request from '../request';

const statisticService = {
  getAllCount: (params) =>
    request.get('dashboard/market.attendant/statistics/count', { params }),
};

export default statisticService;
