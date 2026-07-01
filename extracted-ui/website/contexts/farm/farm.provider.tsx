import { useReducer } from "react";
import { FarmContext } from "./farm.context";
import { removePackerie, setPackerie } from "utils/session";
import { IShop } from "interfaces";
import useShopWorkingSchedule from "hooks/useShopWorkingSchedule";

enum FarmActionKind {
  UPDATE = "UPDATE_FARM",
  RESET = "RESET_FARM",
}

interface FarmAction {
  type: FarmActionKind;
  payload?: any;
}

interface FarmState {
  farm?: IShop;
}

function reducer(state: FarmState, action: FarmAction) {
  const { type, payload } = action;
  switch (type) {
    case FarmActionKind.UPDATE:
      setPackerie("farm", JSON.stringify(payload));
      return {
        ...state,
        farm: payload,
      };
    case FarmActionKind.RESET:
      removePackerie("farm");
      return {};
    default:
      return state;
  }
}

type Props = {
  children: any;
  farmState?: IShop;
};

export function FarmProvider({ children, farmState }: Props) {
  const [state, dispatch] = useReducer(reducer, {
    farm: farmState,
  });

  const { workingSchedule, isShopClosed, isOpen } = useShopWorkingSchedule(
    state.farm
  );

  function updateFarm(data?: IShop) {
    dispatch({ type: FarmActionKind.UPDATE, payload: data });
  }

  function resetFarm() {
    dispatch({ type: FarmActionKind.RESET, payload: null });
  }

  return (
    <FarmContext.Provider
      value={{
        farm: state?.farm,
        updateFarm,
        resetFarm,
        workingSchedule,
        isShopClosed,
        isOpen,
      }}
    >
      {children}
    </FarmContext.Provider>
  );
}
