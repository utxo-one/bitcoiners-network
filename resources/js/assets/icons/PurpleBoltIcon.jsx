export default function PurpleBoltIcon(props) {
  return (
    <svg
      width={47}
      height={53}
      viewBox="0 0 47 53"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
      {...props}
    >
      <g filter="url(#__purple-bolt-gradient-1)">
        <path
          d="m5.013 25.587 25.2-21.312c1.098-.69 2.145 0 1.474 1.184l-8.042 15.524h14.342s2.279 0 0 1.841L13.19 44.268c-1.742 1.447-2.949.658-1.742-1.578l7.774-15.13H5.013s-2.279 0 0-1.973Z"
          fill="url(#__purple-bolt-gradient-2)"
        />
        <path
          d="m5.013 25.587 25.2-21.312c1.098-.69 2.145 0 1.474 1.184l-8.042 15.524h14.342s2.279 0 0 1.841L13.19 44.268c-1.742 1.447-2.949.658-1.742-1.578l7.774-15.13H5.013s-2.279 0 0-1.973Z"
          stroke="#fff"
        />
      </g>
      <defs>
        <linearGradient
          id="__purple-bolt-gradient-2"
          x1={21.5}
          y1={4}
          x2={21.5}
          y2={45}
          gradientUnits="userSpaceOnUse"
        >
          <stop stopColor="#EF3DFF" />
          <stop offset={1} stopColor="#7816F4" />
        </linearGradient>
        <filter
          id="__purple-bolt-gradient-1"
          x={0.5}
          y={0.5}
          width={46}
          height={52}
          filterUnits="userSpaceOnUse"
          colorInterpolationFilters="sRGB"
        >
          <feFlood floodOpacity={0} result="BackgroundImageFix" />
          <feColorMatrix
            in="SourceAlpha"
            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
            result="hardAlpha"
          />
          <feOffset dx={2} dy={2} />
          <feGaussianBlur stdDeviation={2.5} />
          <feComposite in2="hardAlpha" operator="out" />
          <feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.2 0" />
          <feBlend
            in2="BackgroundImageFix"
            result="effect1_dropShadow_153_2186"
          />
          <feBlend
            in="SourceGraphic"
            in2="effect1_dropShadow_153_2186"
            result="shape"
          />
        </filter>
      </defs>
    </svg>
  );
}
