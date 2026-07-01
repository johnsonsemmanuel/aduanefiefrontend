import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { IShop } from "interfaces";
import { RootState } from "redux/store";

export type Favorite FarmsType = {
  favoriteFarms: IShop[];
};

const initialState: Favorite FarmsType = {
  favoriteFarms: [],
};

const favorite FarmSlice = createSlice({
  name: "favoriteFarms",
  initialState,
  reducers: {
    addToLiked(store, action: PayloadAction<IShop>) {
      store.favoriteFarms.push({ ...action.payload });
    },
    removeFromLiked(state, action: PayloadAction<IShop>) {
      const { payload } = action;
      const filtered = state.favoriteFarms.filter(
        (item) => item.uuid !== payload.uuid
      );
      state.favoriteFarms = filtered;
    },
    clearLiked Farms(state) {
      state.favoriteFarms = [];
    },
  },
});

export const { addToLiked, removeFromLiked, clearLiked Farms } =
  favorite FarmSlice.actions;

export const selectLikedFarms = (state: RootState) =>
  state.liked.favoriteFarms;

export default favorite FarmSlice.reducer;
