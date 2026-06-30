import { lazy } from 'react';

const SellerPackhouseRoutes = [
  {
    path: 'seller/packhouse',
    component: lazy(() => import('views/seller-views/packhouse')),
  },
  {
    path: 'seller/packhouse/create',
    component: lazy(() => import('views/seller-views/packhouse/create')),
  },
  {
    path: 'seller/packhouse/edit/:id',
    component: lazy(() => import('views/seller-views/packhouse/edit')),
  },
];

export default SellerPackhouseRoutes;
