import { Button, Card, Select, Space } from 'antd';
import SearchInput from 'components/search-input';
import { useTranslation } from 'react-i18next';
import { InfiniteSelect } from 'components/infinite-select';
import { useState } from 'react';
import shopService from 'services/farm';
import categoryService from 'services/category';
import brandService from 'services/brand';
import { ClearOutlined } from '@ant-design/icons';
import packhouseService from 'services/packhouse';
import { PRODUCT_TYPES } from '../../../../constants';

const ProductListFilter = ({ filters, onChange, onReset }) => {
  const { t } = useTranslation();

  const [hasMore, setHasMore] = useState({
    shop: false,
    category: false,
    brand: false,
    packhouse: false,
  });

  const fetchShops = ({ search, page = 1 }) => {
    const params = {
      search: search || undefined,
      status: 'approved',
      page,
    };
    return shopService.search(params).then((res) => {
      setHasMore({
        ...hasMore,
        shop: res?.meta?.current_page < res?.meta?.last_page,
      });
      return res?.data?.map((item) => ({
        label: item?.translation?.title || item?.id || t('N/A'),
        value: item?.id,
        key: item?.id,
      }));
    });
  };

  const fetchCategories = ({ search, page = 1 }) => {
    const params = {
      type: 'main',
      search: search || undefined,
      page,
      shop_id: filters.shop?.value || undefined,
    };
    return categoryService.search(params).then((res) => {
      setHasMore({
        ...hasMore,
        category: res?.meta?.current_page < res?.meta?.last_page,
      });
      return res?.data?.map((item) => ({
        label: item?.translation?.title || item?.id || t('N/A'),
        value: item?.id,
        key: item?.id,
      }));
    });
  };

  const fetchBrands = ({ search, page = 1 }) => {
    const params = { search: search || undefined, page };
    return brandService.search(params).then((res) => {
      setHasMore({
        ...hasMore,
        brand: res?.meta?.current_page < res?.meta?.last_page,
      });
      return res?.data?.map((item) => ({
        label: item?.title || item?.id || t('N/A'),
        value: item?.id,
        key: item?.id,
      }));
    });
  };

  const fetchPackhouses = ({ search, page = 1 }) => {
    const params = {
      search: search || undefined,
      page,
      active: 1,
    };
    return packhouseService.getAll(params).then((res) => {
      setHasMore({
        ...hasMore,
        packhouse: res?.meta?.current_page < res?.meta?.last_page,
      });
      return res?.data?.map((item) => ({
        label: item?.translation?.title || item?.id || t('N/A'),
        value: item?.id,
        key: item?.id,
      }));
    });
  };

  return (
    <Card>
      <Space wrap>
        <SearchInput
          placeholder={t('search')}
          handleChange={(search) => onChange({ search, page: 1 })}
          defaultValue={filters.search}
          resetSearch={!filters.search}
          style={{ width: 200 }}
        />
        <Select
          allowClear
          placeholder={t('select.type')}
          options={PRODUCT_TYPES.map((item) => ({
            label: t(item),
            value: item,
            key: item,
          }))}
          style={{ minWidth: 200 }}
          value={filters.type}
          onChange={(type) => onChange({ type, page: 1 })}
        />
        <InfiniteSelect
          placeholder={t('select.shop')}
          hasMore={hasMore.shop}
          fetchOptions={fetchShops}
          style={{ minWidth: 200 }}
          value={filters.shop}
          onChange={(shop) => onChange({ shop, page: 1 })}
        />
        <InfiniteSelect
          placeholder={t('select.category')}
          hasMore={hasMore.category}
          style={{ minWidth: 200 }}
          value={filters.category}
          fetchOptions={fetchCategories}
          onChange={(category) => onChange({ category, page: 1 })}
        />
        <InfiniteSelect
          placeholder={t('select.brand')}
          hasMore={hasMore.brand}
          style={{ minWidth: 200 }}
          value={filters.brand}
          fetchOptions={fetchBrands}
          onChange={(brand) => onChange({ brand, page: 1 })}
        />
        <InfiniteSelect
          placeholder={t('select.packhouse')}
          hasMore={hasMore.packhouse}
          style={{ minWidth: 200 }}
          value={filters.packhouse}
          fetchOptions={fetchPackhouses}
          onChange={(packhouse) => onChange({ packhouse, page: 1 })}
        />
        <Button icon={<ClearOutlined />} onClick={onReset}>
          {t('reset.filters')}
        </Button>
      </Space>
    </Card>
  );
};

export default ProductListFilter;
