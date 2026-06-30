import packhouseService from 'services/seller/packhouse';
import { useParams } from 'react-router-dom';
import PackhouseForm from './components/form';

const PackhouseEdit = () => {
  const { id } = useParams();
  const handleCreate = (body) => {
    return packhouseService.update(id, body);
  };
  return <PackhouseForm handleSubmit={handleCreate} />;
};

export default PackhouseEdit;
