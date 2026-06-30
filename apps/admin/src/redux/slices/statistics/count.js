import { createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import statisticService from '../../../services/statistics';
import sellerStatisticService from '../../../services/seller/statistics';
import courierStatisticService from '../../../services/courier/statistics';
import marketAttendantStatisticService from '../../../services/market-attendant/statistics';

const initialState = {
  loading: false,
  counts: {},
  error: '',
  params: {
    type: 'subMonth'
  }
};

export const fetchStatistics = createAsyncThunk(
  'statistics/fetchStatistics',
  (params = {}) => {
    return statisticService.getAll(params).then((res) => res);
  }
);

export const fetchSellerStatisticsCount = createAsyncThunk(
  'statistics/fetchSellerStatisticsCount',
  (params = {}) => {
    return sellerStatisticService.getAll(params).then((res) => res);
  }
);

export const fetchCourierStatisticsCount = createAsyncThunk(
  'statistics/fetchCourierStatisticsCount',
  (params = {}) => {
    return courierStatisticService.getAllCount(params).then((res) => res);
  }
);

export const fetchMarketAttendantStatisticsCount = createAsyncThunk(
  'statistics/fetchMarketAttendantStatisticsCount',
  (params = {}) => {
    return marketAttendantStatisticService.getAllCount(params).then((res) => res);
  }
);

const statisticsCountSlice = createSlice({
  name: 'statisticsCount',
  initialState,
  extraReducers: (builder) => {
    // admin
    builder.addCase(fetchStatistics.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchStatistics.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.counts = payload.data;
      state.error = '';
    });
    builder.addCase(fetchStatistics.rejected, (state, action) => {
      state.loading = false;
      state.counts = {};
      state.error = action.error.message;
    });

    // seller
    builder.addCase(fetchSellerStatisticsCount.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchSellerStatisticsCount.fulfilled, (state, action) => {
      const { payload } = action;

      state.loading = false;
      state.counts = payload.data;
      state.error = '';
    });
    builder.addCase(fetchSellerStatisticsCount.rejected, (state, action) => {
      state.loading = false;
      state.counts = {};
      state.error = action.error.message;
    });

    // delivery
    builder.addCase(fetchCourierStatisticsCount.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(
      fetchCourierStatisticsCount.fulfilled,
      (state, action) => {
        const { payload } = action;
        state.loading = false;
        state.counts = payload.data;
        state.error = '';
      }
    );
    builder.addCase(
      fetchCourierStatisticsCount.rejected,
      (state, action) => {
        state.loading = false;
        state.counts = {};
        state.error = action.error.message;
      }
    );

    // market.attendant
    builder.addCase(fetchMarketAttendantStatisticsCount.pending, (state) => {
      state.loading = true;
    });
    builder.addCase(fetchMarketAttendantStatisticsCount.fulfilled, (state, action) => {
      const { payload } = action;
      state.loading = false;
      state.counts = payload.data;
      state.error = '';
    });
    builder.addCase(fetchMarketAttendantStatisticsCount.rejected, (state, action) => {
      state.loading = false;
      state.counts = {};
      state.error = action.error.message;
    });
  },
  reducers: {
    updateParams: (state, action) => {
      state.params = action.payload
    }
  }
});

export const {updateParams} = statisticsCountSlice.actions

export default statisticsCountSlice.reducer;
