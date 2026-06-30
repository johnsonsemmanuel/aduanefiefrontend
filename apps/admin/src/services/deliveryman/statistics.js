import request from '../request';

const statisticService = {
  getAllCount: (params) =>
    request.get('dashboard/courier/statistics/count', { params }),
};

export default statisticService;
