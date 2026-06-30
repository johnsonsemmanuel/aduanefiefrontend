import React, { useState } from 'react';
import { Button, Col, Form, Modal, Row, Select } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import orderService from 'services/order';
import { setRefetch } from 'redux/slices/menu';
import { useTranslation } from 'react-i18next';
import userService from 'services/user';
import { DebounceSelect } from 'components/search';
import { transactionStatuses } from 'constants/index';

export default function OrderCourier({ orderDetails: data, handleCancel }) {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);

  const [form] = Form.useForm();
  const dispatch = useDispatch();
  const [loading, setLoading] = useState(false);

  const transactionOptions = transactionStatuses.map((item) => ({
    label: t(item),
    value: item,
    key: item,
  }));

  const onFinish = (values) => {
    const params = {
      courier: values?.courier?.value,
      transaction_status:
        values?.transaction_status?.value ||
        values?.transaction_status ||
        undefined,
    };
    setLoading(true);
    orderService
      .updateDelivery(data.id, params)
      .then(() => {
        handleCancel();
        dispatch(setRefetch(activeMenu));
      })
      .finally(() => setLoading(false));
  };

  const fetchDelivery = async (search) => {
    const params = {
      search: search.length ? search : undefined,
      page: 1,
      perPage: 10,
      role: 'courier',
      shop_id: data.shop.id,
      exist_token: 1,
      [`address[latitude]`]: data?.location?.latitude || undefined,
      [`address[longitude]`]: data?.location?.longitude || undefined,
    };
    return userService.getAll(params).then(({ data }) =>
      data.map((item) => ({
        label: item.firstname + ' ' + item.lastname || 'no name',
        value: item.id,
      })),
    );
  };

  return (
    <Modal
      visible={!!data}
      onCancel={handleCancel}
      footer={[
        <Button
          key='saveBtn'
          type='primary'
          onClick={() => form.submit()}
          loading={loading}
        >
          {t('save')}
        </Button>,
        <Button key='cancelBtn' type='default' onClick={handleCancel}>
          {t('cancel')}
        </Button>,
      ]}
    >
      <Form
        form={form}
        layout='vertical'
        onFinish={onFinish}
        initialValues={{
          courier: data?.courier
            ? {
                label: `${data?.courier?.firstname || ''} ${data?.courier?.lastname || ''}`,
                value: data?.courier?.id,
              }
            : undefined,
          transaction_status: data?.transaction?.status,
        }}
      >
        <Row gutter={12}>
          <Col span={24}>
            <Form.Item
              label={t('courier')}
              name='courier'
              rules={[
                {
                  required: true,
                  message: t('required'),
                },
              ]}
            >
              <DebounceSelect
                className='w-100'
                debounceTimeout={500}
                placeholder={t('select.courier')}
                fetchOptions={fetchDelivery}
                allowClear={true}
              />
            </Form.Item>
          </Col>
          {data?.transaction && (
            <Col span={24}>
              <Form.Item
                label={t('transaction.status')}
                name='transaction_status'
              >
                <Select options={transactionOptions} labelInValue />
              </Form.Item>
            </Col>
          )}
        </Row>
      </Form>
    </Modal>
  );
}
