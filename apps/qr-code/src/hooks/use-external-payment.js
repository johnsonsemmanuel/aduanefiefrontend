import { useMutation } from "@tanstack/react-query";
import { fetcher } from "../utils/fetcher.js";

export const useExternalPayment = ({ onSuccess, onError }) => {
  return useMutation({
    mutationKey: ["external-payment"],
    mutationFn: (body) => {
      return fetcher(
        `v1/rest/${body.processType}-${body.paymentType}-process`,
        body.body,
      ).then((res) => {
        if (res.ok) {
          return res.json();
        } else {
          throw res.json();
        }
      });
    },
    onSuccess: (res, body) => {
      if (onSuccess) {
        onSuccess(res, body);
      }
    },
    onError: (err) => {
      if (onError) {
        onError(err);
      }
      console.error("payment process =>", err?.data?.message);
    },
  });
};
