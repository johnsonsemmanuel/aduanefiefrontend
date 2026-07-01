import React, { useState, useEffect } from "react";
import SEO from "components/seo";
import Content from "containers/content/content";
import { GetServerSideProps } from "next";
import informationService from "services/information";
import { useTranslation } from "react-i18next";

type Props = {
  referralData?: any;
};

export default function ReferralTerms({ referralData }: Props) {
  const { t } = useTranslation();
  const [data, setData] = useState(referralData);

  useEffect(() => {
    if (!data) {
      informationService.getReferrals().then((res) => {
        if (res) setData(res);
      }).catch(() => {});
    }
  }, []);

  const content = data?.data?.translation;

  return (
    <>
      <SEO title={t("referral.terms")} />
      <Content
        data={{
          title: t("referral.terms"),
          description: content?.faq,
          locale: content?.locale,
        }}
      />
    </>
  );
}

export const getServerSideProps: GetServerSideProps = async (ctx) => {
  let referralData = null;
  try {
    const res = await informationService.getReferrals();
    referralData = res;
  } catch (e) {
    console.log("Failed to fetch referral terms:", e);
  }

  return {
    props: {
      referralData,
    },
  };
};
