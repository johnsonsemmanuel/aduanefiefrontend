import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import sellerPackhouseService from 'services/seller/packhouse';

const initialState = {
  seller: {
    loading: false,
    packhouses: [],
    error: '',
    params: {
      page: 1,
      perPage: 10,
    },
    meta: {},
  },
};

export const fetchSellerPackhouses = createAsyncThunk(
  'packhouse/fetchSellerPackhouses',
  (params = {}) => sellerPackhouseService.getAll(params).then((res) => res),
);

const packhouseSlice = createSlice({
  name: 'packhouse',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchSellerPackhouses.pending, (state) => {
      state.seller.loading = true;
    });
    builder.addCase(fetchSellerPackhouses.fulfilled, (state, action) => {
      const { payload } = action;
      state.seller.loading = false;
      state.seller.packhouses = payload?.data || [];
      state.seller.meta = payload?.meta || {};
      state.seller.params.page = payload?.meta?.current_page || 1;
      state.seller.params.page = payload?.meta?.per_page || 10;
      state.seller.error = '';
    });
    builder.addCase(fetchSellerPackhouses.rejected, (state, action) => {
      state.seller.loading = false;
      state.seller.error = action?.error?.message;
      state.seller.packhouses = [];
    });
  },
});

export default packhouseSlice.reducer;
