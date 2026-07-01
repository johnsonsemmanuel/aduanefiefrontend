import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { IShop } from "interfaces";
import { RootState } from "redux/store";

export type FavoriteFarmsType = {
  favoriteFarms: IShop[];
};

const initialState: FavoriteFarmsType = {
  favoriteFarms: [],
};

const favoriteFarmsSlice = createSlice({
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
    clearLikedFarms(state) {
      state.favoriteFarms = [];
    },
  },
});

export const { addToLiked, removeFromLiked, clearLikedFarms } =
  favoriteFarmsSlice.actions;

export const selectLikedFarms = (state: RootState) =>
  state.liked.favoriteFarms;

export default favoriteFarmsSlice.reducer;
