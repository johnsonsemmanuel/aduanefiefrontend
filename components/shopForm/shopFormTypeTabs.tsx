import React from "react";
import cls from "./shopForm.module.scss";
import { FormikProps } from "formik";
import { ShopFormType } from "interfaces";
import { useTranslation } from "react-i18next";

type Props = {
  formik: FormikProps<ShopFormType>;
};

export default function ShopFormTypeTabs({ formik }: Props) {
  const { t } = useTranslation();
  const { type } = formik.values;

  return (
    <div className={cls.tabs}>
      <button
        type="button"
        className={`${cls.tab} ${type === "farm" ? cls.active : ""}`}
        onClick={() => formik.setFieldValue("type", "farm")}
      >
        <span className={cls.text}>{t("farm")}</span>
      </button>
      <button
        type="button"
        className={`${cls.tab} ${type === "shop" ? cls.active : ""}`}
        onClick={() => formik.setFieldValue("type", "shop")}
      >
        <span className={cls.text}>{t("shop")}</span>
      </button>
    </div>
  );
}
