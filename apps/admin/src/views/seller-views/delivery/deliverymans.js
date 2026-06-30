import React, { useEffect, useState } from 'react';
import { Card, Table } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import { disableRefetch, setMenuData } from '../../../redux/slices/menu';
// import { fetchSellerCouriers } from '../../../redux/slices/courier';
import useDidUpdate from '../../../helpers/useDidUpdate';
import formatSortType from '../../../helpers/formatSortType';
import { useTranslation } from 'react-i18next';
import FilterColumns from '../../../components/filter-column';

export default function SellerCouriers() {
  const dispatch = useDispatch();
  const { t } = useTranslation();

  const [columns, setColumns] = useState([
    {
      title: t('id'),
      dataIndex: 'id',
      key: 'id',
      sorter: true,
      is_show: true,
    },
    {
      title: t('name'),
      dataIndex: 'firstname',
      key: 'firstname',
      is_show: true,
      render: (firstname, row) => (
        <div>
          {firstname} {row.lastname || ''}
        </div>
      ),
    },
    {
      title: t('phone'),
      dataIndex: 'phone',
      key: 'phone',
      is_show: true,
    },
  ]);

  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const { couriers, meta, loading, params } = useSelector(
    (state) => state.courier,
    shallowEqual
  );

  useEffect(() => {
    if (activeMenu.refetch) {
      // dispatch(fetchSellerCouriers());
      dispatch(disableRefetch(activeMenu));
    }
  }, [activeMenu.refetch]);

  useDidUpdate(() => {
    const data = activeMenu.data;
    const paramsData = {
      sort: data?.sort,
      column: data?.column,
      perPage: data?.perPage,
      page: data?.page,
    };
    // dispatch(fetchSellerCouriers(paramsData));
  }, [activeMenu.data]);

  function onChangePagination(pagination, filters, sorter) {
    const { pageSize: perPage, current: page } = pagination;
    const { field: column, order } = sorter;
    const sort = formatSortType(order);
    dispatch(
      setMenuData({ activeMenu, data: { perPage, page, column, sort } })
    );
  }

  return (
    <Card
      title={t('couriers')}
      extra={<FilterColumns columns={columns} setColumns={setColumns} />}
    >
      <Table
        scroll={{ x: true }}
        columns={columns?.filter((item) => item.is_show)}
        dataSource={couriers}
        pagination={{
          pageSize: params.perPage,
          page: params.page,
          total: meta.total,
          defaultCurrent: params.page,
        }}
        rowKey={(record) => record.id}
        onChange={onChangePagination}
        loading={loading}
      />
    </Card>
  );
}
