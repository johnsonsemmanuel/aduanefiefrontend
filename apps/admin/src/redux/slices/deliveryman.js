import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import userService from '../../services/user';
import sellerUserService from '../../services/seller/user';

const initialState = {
  loading: false,
  couriers: [],
  error: '',
  params: {
    page: 1,
    perPage: 10,
    role: 'courier',
  },
  meta: {},
};

export const fetchCouriers = createAsyncThunk(
  'delivery/fetchCouriers',
  (params = {}) => {
    return userService
      .getAll({ ...initialState.params, ...params })
      .then((res) => res);
  }
);
export const fetchSellerCouriers = createAsyncThunk(
  'delivery/fetchSellerCouriers',
  (params = {}) => {
    return sellerUserService.getCouriers(params).then((res) => res);
  }
);

const courierSlice = createSlice({
  name: 'courier',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchCouriers.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchCouriers.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.couriers = payload.data;
      state.meta = payload.meta;
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(fetchCouriers.rejected, (state, action) => {
      state.loading = false;
      state.couriers = [];
      state.error = action.error.message;
    });

    builder.addCase(fetchSellerCouriers.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchSellerCouriers.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.couriers = payload.data;
      state.meta = payload.meta;
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(fetchSellerCouriers.rejected, (state, action) => {
      state.loading = false;
      state.couriers = [];
      state.error = action.error.message;
    });
  },
});

export default courierSlice.reducer;
