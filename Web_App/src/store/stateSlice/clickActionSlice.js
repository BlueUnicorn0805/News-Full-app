import { createSlice } from "@reduxjs/toolkit";

export const clickActionSlice = createSlice({
  name: "clickAction",
  initialState: {
    searchPopUp: false,
  },
  reducers: {
    SetSearchPopUp: (state, action) => {
      state.searchPopUp = action.payload;
    },
  },
});

export const { SetSearchPopUp } = clickActionSlice.actions;
export default clickActionSlice.reducer;
