import React, { useEffect, useState } from 'react';
import { Button, Col, Form, Modal, Row, Select } from 'antd';
import { shallowEqual, useDispatch, useSelector } from 'react-redux';
import orderService from 'services/seller/order';
import { setRefetch } from 'redux/slices/menu';
import { useTranslation } from 'react-i18next';
import { fetchSellerCouriers } from 'redux/slices/courier';
import { DebounceSelect } from 'components/search';
import sellerDeliverymenService from 'services/seller/user';
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

  useEffect(() => {
    dispatch(fetchSellerCouriers({}));
    // eslint-disable-next-line
  }, []);

  const fetchDeliverymen = (search) => {
    const paramsData = {
      perPage: 10,
      page: 1,
      search: search?.length ? search : undefined,
      [`address[latitude]`]: data?.location?.latitude || undefined,
      [`address[longitude]`]: data?.location?.longitude || undefined,
    };
    return sellerDeliverymenService.getCouriers(paramsData).then((res) => {
      return res.data.map((delivery) => ({
        label: delivery?.firstname,
        value: delivery?.id,
      }));
    });
  };

  return (
    <Modal
      visible={!!data}
      title={data.title}
      onCancel={handleCancel}
      footer={[
        <Button type='primary' onClick={() => form.submit()} loading={loading}>
          {t('save')}
        </Button>,
        <Button type='default' onClick={handleCancel}>
          {t('cancel')}
        </Button>,
      ]}
    >
      <Form
        form={form}
        layout='vertical'
        onFinish={onFinish}
        initialValues={{
          courier: {
            label: data?.courier?.firstname,
            value: data.courier?.id,
            key: data.courier?.id,
          },
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
              <DebounceSelect fetchOptions={fetchDeliverymen} />
            </Form.Item>
          </Col>
          {data?.transaction && (
            <Col span={24}>
              <Form.Item
                label={t('transaction.status')}
                name='transaction_status'
              >
                <Select options={transactionOptions} />
              </Form.Item>
            </Col>
          )}
        </Row>
      </Form>
    </Modal>
  );
}
