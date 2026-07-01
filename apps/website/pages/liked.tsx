import React from "react";
import SEO from "components/seo";
import BannerContainer from "containers/banner/banner";
import { useTranslation } from "react-i18next";
import dynamic from "next/dynamic";
import { useAppSelector } from "hooks/useRedux";
import { selectLikedFarms } from "redux/slices/favoriteFarms";
import { useQuery } from "react-query";
import storyService from "services/story";
import bannerService from "services/banner";

const FooterMenu = dynamic(() => import("containers/footerMenu/footerMenu"));
const ShopList = dynamic(() => import("containers/shopList/shopList"), {
  ssr: false,
});
const Empty = dynamic(() => import("components/empty/empty"));

type Props = {};

export default function Liked({}: Props) {
  const { t, i18n } = useTranslation();
  const locale = i18n.language;
  const favoriteFarms = useAppSelector(selectLikedFarms);

  const { data: stories, isLoading: isStoriesLoading } = useQuery(
    ["stories", locale],
    () => storyService.getAll()
  );

  const { data: banners, isLoading: isBannerLoading } = useQuery(
    ["banners", locale],
    () => bannerService.getAll()
  );

  return (
    <>
      <SEO />
      <div style={{ minHeight: "90vh" }}>
        <BannerContainer
          stories={stories || []}
          banners={banners?.data || []}
          loadingStory={isStoriesLoading}
          loadingBanner={isBannerLoading}
        />
        <ShopList title={t("liked.farms")} shops={favoriteFarms} />
        {favoriteFarms.length < 1 && (
          <Empty
            text={t("no.liked.farms")}
            buttonText={t("go.to.menu")}
          />
        )}
      </div>
      <FooterMenu />
    </>
  );
}
