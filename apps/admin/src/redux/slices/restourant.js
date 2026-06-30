import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import farmService from '../../services/farm';

const initialState = {
  loading: false,
  restourants: [],
  error: '',
  params: {
    page: 1,
    perPage: 10,
    type: 'farm',
    role: 'seller',
  },
  meta: {},
};

export const fetchRestourant = createAsyncThunk(
  'shop/fetchRestourant',
  (params = {}) => {
    return farmService
      .getAll({ ...initialState.params, ...params })
      .then((res) => res);
  }
);

const restourantSlice = createSlice({
  name: 'restourants',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchRestourant.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchRestourant.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.restourants = payload.data.map((item) => ({
        created_at: item.created_at,
        active: item.show_type,
        tax: item.tax,
        open: item.open,
        name: item.translation !== null ? item.translation?.title : 'no name',
        seller: item.seller
          ? item.seller.firstname + ' ' + item.seller.lastname
          : '',
        uuid: item.uuid,
        logo_img: item.logo_img,
        back: item.background_img,
        id: item.id,
        status: item.status,
        locales: item.locales,
        deleted_at: item.deleted_at,
      }));
      state.meta = payload.meta;
      state.params.page = payload.meta.current_page;
      state.params.perPage = payload.meta.per_page;
      state.error = '';
    });
    builder.addCase(fetchRestourant.rejected, (state, action) => {
      state.loading = false;
      state.restourants = [];
      state.error = action.error.message;
    });
  },
});

export default restourantSlice.reducer;
