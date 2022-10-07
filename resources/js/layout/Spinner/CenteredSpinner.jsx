import Spinner from './Spinner';
import './CenteredSpinner.scss';

export default function CenteredSpinner({ ...props }) {
  return (
    <div className="__centered-spinner" {...props}>
      <Spinner />
    </div>
  );
}
