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

  return (
    <div className={cls.tabs}>
      <button type="button" className={`${cls.tab} ${cls.active}`}>
        <span className={cls.text}>{t("farm")}</span>
      </button>
    </div>
  );
}
