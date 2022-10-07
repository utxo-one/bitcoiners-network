import './Checkbox.scss';

export default function Checkbox({ ...props }) {
  return (
    <input type="checkbox" className="__checkbox" {...props} />
  );
}
