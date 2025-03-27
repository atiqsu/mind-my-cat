import React, { useEffect, useState } from 'react';
import DateJ, { getDayNameKey, addMinutesToDate, formatYmdHms, makeKeyFromDates, formatHmA } from '../DateJ';

const SitterCalendar = ({ selectedSlot, availableSlots, bookedSlot, onTimeSlotClick }) => {

    const [availability, setAvailability] = useState(null);
    const [clickedSlots, setClickedSlots] = useState([]);
 
    let dayKey = getDayNameKey((new Date(selectedSlot.date)));
    let slots = [];


    useEffect(() => {
        setAvailability({key: dayKey, value: availableSlots[dayKey]});
    }, [dayKey, selectedSlot]);

    useEffect(() => {
        setClickedSlots([...bookedSlot]);
    }, [bookedSlot]);


    const handleClick = (slot) => {

        let updatedClickedSlots;

        if (clickedSlots.includes(slot.key)) {
            updatedClickedSlots = clickedSlots.filter(s => s !== slot.key);
        } else {
            updatedClickedSlots = [...clickedSlots, slot.key];
        }

        setClickedSlots(updatedClickedSlots);
        onTimeSlotClick(updatedClickedSlots);
    }   

  

    if(availability && availability.value) {
        availability.value.forEach(slot => {

            let startTime = slot.start;
            let endTime = slot.end;
            let dt = new Date(selectedSlot.date + ' ' + startTime);
            let endDt = new Date(selectedSlot.date + ' ' + endTime);

            while(dt < endDt) {

                slots.push({
                    from: formatHmA(dt),
                    to: formatHmA(addMinutesToDate(dt, selectedSlot.slot)),
                    key: makeKeyFromDates(dt, addMinutesToDate(dt, selectedSlot.slot)),
                    dt: dt
                });

                dt = addMinutesToDate(dt, selectedSlot.slot);
            }
        });
    }


    console.log(clickedSlots, 'Rerendering...from sitter calendar');
 
    return (
    <div className='grid_slot'>

      <p>Interval: {selectedSlot.slot}</p>
      <p>{selectedSlot.date ? 'Date: ' + selectedSlot.date : 'No slot selected'}</p>

      <div className='grid_slot_container'>
        {slots.map((slot, index) => (
            <div key={index} className={`grid_slot_inner clicable_slot ${clickedSlots.includes(slot.key) ? 'selected_slot' : ''}`} onClick={() => handleClick(slot)}>
                <p>{slot.from} to {slot.to}</p>
            </div>
        ))}
      </div>

    </div>
  );
};

export default SitterCalendar;


