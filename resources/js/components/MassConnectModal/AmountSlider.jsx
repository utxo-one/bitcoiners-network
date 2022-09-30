import * as Slider from '@radix-ui/react-slider';

import './AmountSlider.scss';

export default function AmountSlider({ ...props }) {
  return (
    <Slider.Root className='__amount-slider' {...props}>
      <Slider.Track className='__amount-slider-track'>
        <Slider.Range className='__amount-slider-range' />
      </Slider.Track>
      <Slider.Thumb className='__amount-slider-thumb' />
    </Slider.Root>
  );
}
