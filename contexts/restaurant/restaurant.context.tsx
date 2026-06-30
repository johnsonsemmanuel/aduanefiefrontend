import { IShop } from "interfaces";
import { createContext, useContext } from "react";
import { ShopWorkingDays } from "interfaces";

type  FarmContextType = {
  farm?: IShop;
  update Farm: (data: IShop) => void;
  reset Farm: () => void;
  workingSchedule?: ShopWorkingDays;
  isShopClosed: boolean;
  isOpen: boolean;
};

export const  FarmContext = createContext< FarmContextType>(
  {} as  FarmContextType
);

export const use Farm = () => useContext( FarmContext);
