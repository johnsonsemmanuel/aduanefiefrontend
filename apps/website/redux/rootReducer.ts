import cart from "./slices/cart";
import currency from "./slices/currency";
import favoriteFarms from "./slices/favoriteFarms";
import shopFilter from "./slices/shopFilter";
import userCart from "./slices/userCart";
import product from "./slices/product";
import chat from "./slices/chat";
import search from "./slices/search";
import order from "./slices/order";

const rootReducer = {
  liked: favoriteFarms,
  cart: cart,
  filter: shopFilter,
  currency: currency,
  userCart: userCart,
  product: product,
  chat: chat,
  search: search,
  order: order,
};

export default rootReducer;
