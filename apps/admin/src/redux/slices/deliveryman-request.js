import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';

import requestAdminModelsService from 'services/request-models';

const initialState = {
  loading: false,
  courierRequestData: [],
  error: '',
  params: {
    page: 1,
    perPage: 10,
    type: 'user',
  },
  meta: {},
};

export const fetchCourierRequest = createAsyncThunk(
  'courierRequest/fetchCourierRequest',
  (params = {}) => {
    return requestAdminModelsService.getAll({
      ...initialState.params,
      ...params,
    });
  },
);

const courierRequestSlice = createSlice({
  name: 'courierRequest',
  initialState,
  extraReducers: (builder) => {
    builder.addCase(fetchCourierRequest.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchCourierRequest.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.courierRequestData = payload.data;
      state.meta = payload.meta;
    });
    builder.addCase(fetchCourierRequest.rejected, (state, action) => {
      state.loading = false;
      state.courierRequestData = [];
      state.error = action.error.message;
    });
  },
});

export default courierRequestSlice.reducer;
