import { IShop } from "interfaces";
import { createContext, useContext } from "react";
import { ShopWorkingDays } from "interfaces";

type FarmContextType = {
  farm?: IShop;
  updateFarm: (data: IShop) => void;
  resetFarm: () => void;
  workingSchedule?: ShopWorkingDays;
  isShopClosed: boolean;
  isOpen: boolean;
};

export const FarmContext = createContext<FarmContextType>(
  {} as FarmContextType
);

export const useFarm = () => useContext(FarmContext);
