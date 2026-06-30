// ** React Imports
import { lazy } from 'react';

const MarketAttendantOrderRoutes = [
  {
    path: 'market.attendant/orders',
    component: lazy(() => import('views/market.attendant-views/order')),
  },
  {
    path: 'market.attendant/orders/:type',
    component: lazy(() => import('views/market.attendant-views/order')),
  },
  {
    path: 'market.attendant/orders-board',
    component: lazy(() => import('views/market.attendant-views/order/orders-board')),
  },
  {
    path: 'market.attendant/orders/generate-invoice/:id',
    component: lazy(() => import('components/check')),
  },
  {
    path: 'market.attendant/orders-board/:type',
    component: lazy(() => import('views/market.attendant-views/order/orders-board')),
  },
  {
    path: 'market.attendant/order/details/:id',
    component: lazy(() => import('views/market.attendant-views/order/order-details')),
  },
  {
    path: 'market.attendant/order/:id',
    component: lazy(() => import('views/market.attendant-views/order/order-edit')),
  },
];

export default MarketAttendantOrderRoutes;
