// ** React Imports
import { lazy } from 'react';

const RestraurantRoutes = [
  {
    path: 'farms',
    component: lazy(() => import('views/farm')),
  },
  {
    path: 'farm/add',
    component: lazy(() => import('views/farm/farm-add')),
  },
  {
    path: 'farm/:uuid',
    component: lazy(() => import('views/farm/farm-edit')),
  },
  {
    path: 'farm-clone/:uuid',
    component: lazy(() => import('views/farm/farm-clone')),
  },
];

export default RestraurantRoutes;
