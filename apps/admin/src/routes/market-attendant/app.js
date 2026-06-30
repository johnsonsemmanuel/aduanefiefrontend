// ** React Imports
import { lazy } from 'react';

const MarketAttendantAppRoutes = [
  {
    path: 'my-shop',
    component: lazy(() => import('views/market.attendant-views/order')),
  },
];

export default MarketAttendantAppRoutes;
