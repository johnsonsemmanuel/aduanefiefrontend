import React, { useState } from 'react';
import { shallowEqual, useSelector } from 'react-redux';
import LanguageList from '../../components/language-list';
import { useTranslation } from 'react-i18next';
import { Card, Steps } from 'antd';
import FarmMain from './main';
import FarmDelivery from './farmDelivery';
import Map from '../../components/shop/map';

const { Step } = Steps;

export const steps = [
  {
    title: 'farm',
    content: 'First-content',
  },
  {
    title: 'map',
    content: 'Second-content',
  },
  {
    title: 'delivery',
    content: 'Third-content',
  },
];

const FarmAdd = () => {
  const { t } = useTranslation();
  const { activeMenu } = useSelector((state) => state.menu, shallowEqual);
  const [current, setCurrent] = useState(activeMenu.data?.step || 0);

  const next = () => {
    const step = current + 1;
    setCurrent(step);
  };
  const prev = () => {
    const step = current - 1;
    setCurrent(step);
  };

  return (
    <Card title={t('add.farm')} extra={<LanguageList />}>
      <Steps current={current}>
        {steps.map((item) => (
          <Step title={t(item.title)} key={item.title} />
        ))}
      </Steps>

      <div className='steps-content'>
        {steps[current].content === 'First-content' && (
          <FarmMain next={next} user={false} />
        )}

        {steps[current].content === 'Second-content' && (
          <Map next={next} prev={prev} />
        )}

        {steps[current].content === 'Third-content' && (
          <FarmDelivery next={next} prev={prev} />
        )}
      </div>
    </Card>
  );
};
export default FarmAdd;
