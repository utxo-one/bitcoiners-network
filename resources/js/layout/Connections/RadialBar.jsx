import './RadialBar.scss';

// Inspiration: https://css-tricks.com/building-progress-ring-quickly/
export default function RadialBar({ percent }) {
  const radius = 45;
  const stroke = 7;

  const normalizedRadius = radius - stroke * 2;
  const circumference = normalizedRadius * 2 * Math.PI;

  const strokeDashoffset = circumference - percent / 100 * circumference;

  //  viewBox={`0 0 ${radius * 2} ${radius * 2}`
  return (
    <svg className='__radial-bar' height={radius * 2} width={radius * 2} viewBox={`10 10 70 70`}>
      <circle
        className='background-bar'
        strokeWidth={ stroke }
        r={ normalizedRadius }
        cx={ radius }
        cy={ radius }
      />

      <circle
        className='filled-bar'
        strokeWidth={ stroke }
        strokeDasharray={ circumference + ' ' + circumference }
        style={ { strokeDashoffset } }
        r={ normalizedRadius }
        cx={ radius }
        cy={ radius }
      />
  </svg>
  );
}
