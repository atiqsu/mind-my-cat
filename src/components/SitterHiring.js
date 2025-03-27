
import React, { useEffect, useState, useCallback } from 'react';
import { format, addDays, isAfter, isBefore, parseISO } from 'date-fns';
import Request from '../Request';
import { getTimeSlotFromPriceInfo } from '../helper';
import SitterCalendar from './SitterCalendar';
import { getTimeFromKey, isDateStringIsEqualToDate, getYmdFromDateKey, timestampFromKey, getYmdHmsFromKey } from '../DateJ';


const SearchResults = ( {userId} ) => {

    const [filterIdd, setFilterIdd] = useState(null);
    const [sitterId, setSitterId] = useState(0);
    const [startDate, setStartDate] = useState(null);
    const [endDate, setEndDate] = useState(null);
    const [gridData, setGridData] = useState([]);
    const [slots, setSlots] = useState([]);
    const [sitterTimeTable, setSitterTimeTable] = useState([]);

    const [currentSelection, setCurrentSelection] = useState({});
    const [selectedSlot, setSelectedSlot] = useState('');

    const [bookedSlot, setBookedSlot] = useState([]);

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [isLoggedIn, setIsLoggedIn] = useState(false);
    const [userRole, setUserRole] = useState('');
    const [userIdd, setUserIdd] = useState('');


    useEffect(() => {

        const search = window.location.search;
        const urlParams = new URLSearchParams(search);
        const filterIddValue = urlParams.get('filter_idd');
        const sitterIdValue = urlParams.get('sitter_id');

        if(sitterIdValue) {
            setSitterId(sitterIdValue);
        }
    
        if (filterIddValue) {
          setFilterIdd(filterIddValue);
        } 
        
        if(!filterIddValue || !sitterIdValue) {
          setLoading(false);
          setError('No filter_idd or sitter_id parameter found in the URL.');
        }

        console.log('in useEffect-isLoggedIn', window.bpcFrontObj.mmcUser);
        if(window.bpcFrontObj.mmcUser.isLoggedIn) {
            setIsLoggedIn(window.bpcFrontObj.mmcUser.isLoggedIn);
            setUserRole(window.bpcFrontObj.mmcUser.userRole);
            setUserIdd(window.bpcFrontObj.mmcUser.userIdd);
        }

      }, []
    );


    const fetchData = useCallback( async (filterIdd, sitterId) => {
        
        setLoading(true);
        setError(null);

        Request.post(null, {
            action: 'mmc_get_sitter_info',
            user_req: filterIdd,
            sitter: sitterId,

        }).then(res => {

            let slots = getTimeSlotFromPriceInfo(res.service_pricing);

            setStartDate(res.start_date);
            setEndDate(res.end_date);
            setSlots(slots);
            setSitterTimeTable(res.sitter_availability);

        }).catch(err => {

            setLoading(false);
            setError('Failed to fetch data. Please try again later.');

        }).finally(() => {
            setLoading(false);
        });

    }, []);



    useEffect(() => {

        console.log('in useEffect-fetchData', filterIdd, sitterId);

        if(filterIdd && sitterId) {
            fetchData(filterIdd, sitterId);
        }

      },[filterIdd, sitterId]
    );

    useEffect(() => {

        console.log('in useEffect-generateGrid', startDate, endDate);

            if (startDate && endDate) {
                generateGrid(startDate, endDate);
            }
        }, [startDate, endDate]
    );


    const generateGrid = (start, end) => {

        let currentDate = new Date(start);
        const newGridData = [];

        while (!isAfter(currentDate, end)) {

          newGridData.push(formatYmd(currentDate));
          currentDate = addDays(currentDate, 1);
        }

        console.log('in generateGrid', newGridData);

        setGridData(newGridData);
    };


    const handleAddGrid = () => {

        if (gridData.length > 0) {
          const lastDate = gridData[gridData.length - 1];
          const nextDate = addDays(new Date(lastDate), 1);

          setGridData([...gridData, formatYmd(nextDate)]);

        } else if (startDate && endDate) {
          // If grid is empty but we have start and end date, add a day after the end date
          const nextDate = addDays(new Date(endDate), 1);
          setGridData([nextDate]);
          setEndDate(nextDate); 
          // Optionally update the end date state
        } else {
          alert('Please wait for initial data to load or ensure there are existing grid items.');
        }
    };


    const handleDeleteGrid = (indexToDelete) => {

        let date = gridData[indexToDelete];
        const newGridData = gridData.filter((_, index) => index !== indexToDelete);
        setGridData(newGridData);

        if (newGridData.length > 0) {
          // Optionally update start or end date if the first or last item is deleted
          const firstDate = newGridData[0];
          const lastDate = newGridData[newGridData.length - 1];
          setStartDate(firstDate);
          setEndDate(lastDate);
        } else {
          setStartDate(null);
          setEndDate(null);
        }

        handleBookedSlotOnDateGridDelete(date);
    };

    const handleBookedSlotOnDateGridDelete = (date) => {

        let newBookedSlot = bookedSlot.filter(slot => !isDateStringIsEqualToDate(slot, date));

        console.log('in handleBookedSlotOnDateGridDelete', newBookedSlot);

        setBookedSlot(newBookedSlot);
    }
      
    const handleSave = async () => {

        setLoading(true);

        if(bookedSlot.length === 0) {

            setError('No booked slot found.');
            setLoading(false);
            return;
        }

        let bookingData = {};

        bookedSlot.forEach(slot => {

            let date = getYmdFromDateKey(slot);

            if(!bookingData[date]) {
                bookingData[date] = [];
            }

            bookingData[date].push({
                start: timestampFromKey(slot),
                end: timestampFromKey(slot, 1)
            });
        });

        //console.log(bookedSlot, 'in handleSave', bookingData, Date.now());
        //handleSave

        setLoading(true);
        setError(null);

        Request.post(null, {
            action: 'mmc_save_owner_requested_schedule',
            filter_id: filterIdd,
            sitter_id: sitterId,
            scheduled: bookingData,

        }).then(res => {

            //contract_id
            
            console.log('in handleSave', res);
            
        }).catch(err => {

            setLoading(false);
            setError('Failed to fetch data. Please try again later.');
            console.log('in handleSave catch', err);

        }).finally(() => {
            setLoading(false);
        });
    };


    const handleSelectChange = (event) => {

        const date = event.target.dataset.date;
        const slot = event.target.value;

        setSelectedSlot(slot);
        setCurrentSelection({date, slot});
    };


    const handleTimeSlotClick = (data) => {

        let newBookedSlot = [...data];

        setBookedSlot(newBookedSlot);
    };



    const formatYmd = (date) => {
        return format(date, 'yyyy-MM-dd');
    }

    const formatYmd2 = (date) => {
        return format(date, 'yyyyMMdd');
    }

    const isSitterAvailableByDate = (date) => {

        let holidayKey = 'hodidays';
        let isHoliday = false;

        if(sitterTimeTable[holidayKey]) {

            let ymd = formatYmd2(date);

            sitterTimeTable[holidayKey].forEach(holiday => {

                if(holiday.dates === ymd) {
                    isHoliday = true;

                    return false;
                }
            });
        }

        if(isHoliday) {
            return false;
        }

        ///console.log('in isSitterAvailableByDate', date, typeof date);

        let dayKey = getDayNameKey(new Date(date));

        if(!sitterTimeTable[dayKey]) {
            return false;
        }

        return true;
    };
    


      console.log('Re-rendering...from sitter hiring', bookedSlot);

	return (

		<div>
			<h2>Sitter Profile Id:   {userId}</h2>

            {error && <p style={{color: 'red', fontWeight: 'bold', backgroundColor: 'yellow', padding: '10px'}}>{error}</p>} 
            {loading && <p style={{color: 'blue', fontWeight: 'bold', backgroundColor: 'lightyellow', padding: '10px'}}>Loading...</p>} 

            <div className='flx-cont'>
                <div className='boom-col'>
                    
                    {gridData && gridData.length > 0 ? (
                        <div className='grid_slot_container'>
                            {gridData.map((date, index) => (
                                <div key={index} className='grid_slot'>
                                    <div className='grid_slot_header'>
                                        {format(date, 'yyyy-MM-dd')} ({format(date, 'EEE')})
                                        <button onClick={() => handleDeleteGrid(index)}>Delete</button>
                                    </div>

                                    <div className='grid_slot_inner'>

                                        {isSitterAvailableByDate(date) ?  (

                                            <select data-date={date} onChange={handleSelectChange}>
                                                <option value=''>Select a slot</option>
                                                {slots.map((slot, index) => (
                                                    <option key={index}>{slot}</option>
                                                ))}
                                            </select>

                                        ) : (
                                            <span>Sitter is not available on this date</span>
                                        )}
                                        
                                    </div>

                                    {bookedSlot.length > 0 && bookedSlot.map((slot, index) => ( 

                                        isDateStringIsEqualToDate(slot, date) && (
                                            <div className='grid_slot_inner bg1'>
                                                <p key={index}>{getTimeFromKey(slot)}</p>
                                            </div>
                                        )
                                    ))}

                                </div>
                            ))}
                                    
                        </div>
                    ) : (
                        <p>No dates in the grid.</p>
                    )}
                    
                    <button className='btn-add' onClick={handleAddGrid}>Add More Grid</button>

                    {isLoggedIn ? (
                        <button disabled={loading} className='btn-submit' onClick={handleSave}>Save Data</button>
                    ) : (
                        <button className='btn-submit' >Please login to hire</button>
                    )}

                  
                </div>

                <div className='boom-col'>
                    <p>Selected Slot(parent): {selectedSlot}</p>

                    {selectedSlot && (
                        <SitterCalendar bookedSlot={bookedSlot} theDate={selectedSlot} selectedSlot={currentSelection} availableSlots={sitterTimeTable} onTimeSlotClick={handleTimeSlotClick} />
                    )}

                </div>
            </div>

		</div>
	);
};



export default SearchResults;
