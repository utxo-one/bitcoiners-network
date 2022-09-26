export default function PointyArrow(props) {
  return (
    <svg
      width={32}
      height={24}
      viewBox="0 0 32 24"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      {...props}
    >
      <path
        d="M30 13.5a1.5 1.5 0 0 0 0-3v3ZM.94 10.94a1.5 1.5 0 0 0 0 2.12l9.545 9.547a1.5 1.5 0 1 0 2.122-2.122L4.12 12l8.486-8.485a1.5 1.5 0 1 0-2.122-2.122L.94 10.94ZM30 10.5H2v3h28v-3Z"
        fill="#6C6C6C"
      />
    </svg>
  );
}
