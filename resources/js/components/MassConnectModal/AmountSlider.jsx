import * as Slider from '@radix-ui/react-slider';

import './AmountSlider.scss';

export default function AmountSlider({ value, onValueChange, max }) {
  return (
    <Slider.Root value={value} onValueChange={onValueChange} max={max} className='__amount-slider'>
      <Slider.Track className='__amount-slider-track'>
        <Slider.Range className='__amount-slider-range' />
      </Slider.Track>
      <Slider.Thumb className='__amount-slider-thumb' />
    </Slider.Root>
  );
}
