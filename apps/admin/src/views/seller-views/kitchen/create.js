import packhouseService from 'services/seller/packhouse';
import PackhouseForm from './components/form';

const PackhouseCreate = () => {
  const handleCreate = (body) => {
    return packhouseService.create(body);
  };
  return <PackhouseForm handleSubmit={handleCreate} />;
};

export default PackhouseCreate;
