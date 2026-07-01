import React, { useState, useEffect } from "react";
import SEO from "components/seo";
import BlogContent from "containers/blogContent/blogContent";
import { useRouter } from "next/router";
import blogService from "services/blog";
import { GetStaticPaths, GetStaticProps } from "next";
import getImage from "utils/getImage";
import { useTranslation } from "react-i18next";
import { getPackerie } from "utils/session";
import getLanguage from "utils/getLanguage";

type Props = {
  initialBlogData?: any;
};

export default function BlogSingle({ initialBlogData }: Props) {
  const { i18n } = useTranslation();
  const locale = i18n.language;
  const { query } = useRouter();
  const blogId = String(query.id);

  const [data, setData] = useState(initialBlogData);

  useEffect(() => {
    if (!data) {
      blogService.getById(blogId).then((res) => {
        if (res) setData(res);
      }).catch(() => {});
    }
  }, [blogId]);

  return (
    <>
      <SEO
        title={data?.data?.translation?.title}
        description={data?.data?.translation?.short_desc}
        image={getImage(data?.data?.img)}
      />
      <BlogContent data={data?.data} />
    </>
  );
}

export const getStaticProps: GetStaticProps = async (ctx) => {
  const { params } = ctx;
  const locale = getLanguage(getPackerie("locale", ctx));

  let initialBlogData = null;
  try {
    const res = await blogService.getById(String(params?.id));
    initialBlogData = res;
  } catch (e) {
    console.log("Failed to prefetch blog:", e);
  }

  return {
    props: {
      initialBlogData,
    },
    revalidate: 3600,
  };
};

export const getStaticPaths: GetStaticPaths = async () => {
  return {
    paths: [],
    fallback: "blocking",
  };
};
