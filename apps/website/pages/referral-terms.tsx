import React from "react";
import SEO from "components/seo";
import Content from "containers/content/content";
import { dehydrate, QueryClient, useQuery } from "react-query";
import { GetStaticProps } from "next";
import informationService from "services/information";
import { useTranslation } from "react-i18next";
import { getPackerie } from "utils/session";
import getLanguage from "utils/getLanguage";

type Props = {};

export default function ReferralTerms({}: Props) {
  const { t, i18n } = useTranslation();
  const locale = i18n.language;

  const { data } = useQuery(["referral-terms", locale], () =>
    informationService.getReferrals().catch(() => null),
  );

  return (
    <>
      <SEO title={t("referral.terms")} />
      <Content
        data={{
          title: t("referral.terms"),
          description: data?.data?.translation?.faq,
          locale: data?.data?.translation?.locale,
        }}
      />
    </>
  );
}

export const getStaticProps: GetStaticProps = async (ctx) => {
  const queryClient = new QueryClient();
  const locale = getLanguage(getPackerie("locale", ctx));

  try {
    await queryClient.prefetchQuery(["referral-terms", locale], () =>
      informationService.getReferrals(),
    );
  } catch (e) {
    console.log("Failed to prefetch referral terms:", e);
  }

  return {
    props: {
      dehydratedState: JSON.parse(JSON.stringify(dehydrate(queryClient))),
    },
  };
};
