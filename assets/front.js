

document.addEventListener('DOMContentLoaded', function() {

    //console.log('Loaded the front js.....');
    const frontObj = window.bpcFrontObj;

    let schedulePrevisitDom = document.querySelector('#mmc_schedule_previsit');
    let confirmPrevisitDom = document.querySelector('#mmc_confirm_previsit_date');
    let rejectPrevisitDom = document.querySelector('#mmc_reject_previsit_date');
    let sessionStartDom = document.querySelector('#mmc_session_start');
    let sessionEndDom = document.querySelector('#mmc_session_end');


    if ( schedulePrevisitDom ) {

        schedulePrevisitDom.addEventListener('click', function() {

            let $ = jQuery;

            let previsitDate = $('#mmc_previsit_date').val();
            let contractId = $('#mmc_contract_id').val();

            if ( !previsitDate || !contractId ) {
                alert('Please select a date or a valid contract');
                return;
            }

            $.ajax({
                url: frontObj.ajax_url,
                type: 'POST',
                data: {
                    action: 'mmc_set_previsit_date',
                    previsit_date: previsitDate,
                    contract_id: contractId
                },
                success: function(response) {

                    location.reload();
                },
                error: function(error) {
                    console.log('error...', error);
                    alert('Error scheduling previsit date');
                }
            })
        });
    }

    if ( confirmPrevisitDom ) {

        confirmPrevisitDom.addEventListener('click', function() {

            let $ = jQuery;

            let contractId = $('#mmc_contract_id').val();

            console.log('contractId...', contractId);

            $.ajax({
                url: frontObj.ajax_url,
                type: 'POST',
                data: {
                    action: 'mmc_confirm_previsit_date',
                    contract_id: contractId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.log('error...', error);
                    alert('Error confirming previsit date');
                }
            })

        });
    }

    if ( rejectPrevisitDom ) {

        rejectPrevisitDom.addEventListener('click', function() {

            if ( !confirm('Are you sure you want to reject the previsit date?') ) {
                return;
            }

            let $ = jQuery;

            let contractId = $('#mmc_contract_id').val();

            $.ajax({
                url: frontObj.ajax_url,
                type: 'POST',
                data: {
                    action: 'mmc_reject_previsit_date',
                    contract_id: contractId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.log('error...', error);
                    alert('Error confirming previsit date');
                }
            })
        });
    }

    if ( sessionStartDom ) {

        sessionStartDom.addEventListener('click', function() {

            if ( !confirm('Are you sure you want to start the session?') ) {
                return;
            }   

            let $ = jQuery;

            let contractId = $('#mmc_contract_id').val();
            
            $.ajax({
                url: frontObj.ajax_url,
                type: 'POST',
                data: {
                    action: 'mmc_start_session',
                    contract_id: contractId
                },
                success: function(response) {
                    location.reload();
                },
                error: function(error) {
                    console.log('error...', error);
                    alert('Error starting session');
                }
            })
        });
    } 


    


});
    


function generateUserRequestString() {

    const randomNumber = Math.floor(Math.random() * 900) + 100;

    const timestamp = Date.now();

    const requestString = `user_req_${randomNumber}_${timestamp}`;

    return requestString;
}


function updatePetListOnServiceChange(serviceId, data, $, updateTarget = 'pet_number_section') {

    data = data || allServiceData;

    let selected = data[serviceId];
    let petTypes = selected['_pet_type'] || false;
    let htm = '';

    let returnData = [];

    if(petTypes) {

        petTypes.forEach((pet, idx) => {

            htm += '<div class="flx-display"><input type="number" name="pet_number_' + pet['slug'] + '"> <label>' + pet['name'] + '(s)</label></div>';

            returnData.push(pet);
        });
    }

    htm += '<div class="flx-display"><input type="number" name="pet_number_others"> <label> Other(s) </label></div>';

    $('#'+updateTarget).html(htm);

    return returnData;
}

function updateDurationOnServiceChange(serviceId, data, $, updateTarget = 'duration_per_day') {

    let selected = data[serviceId];
    let list = selected['_pricing']['prices'] || [];

    let htm = '';   
    let returnData = {};


    list.forEach((item, idx) => {
        htm += '<option value="' + (idx+1) + '">' + item['minutes'] + ' minute(s)</option>';

        returnData[idx+1] = item['minutes'];
    });

    $('#'+updateTarget).html(htm);

    return returnData;
}


function submitBtnDisabler(val, event, $) {

    $(event.currentTarget).prop("disabled", val);
}

function getPricebreakdown() {

}


function printSitterCalendarForDateRange($config, modal, $) {


    $('#mmc-popup-content').show();
    
    let startDate = $config.startDate;
    let endDate = $config.endDate;
    let sitterId = $config.sitterId;
    let priceInfo = getPriceInfoObj($config.priceInfo);
    // give me code for prinitng calendar like google calendar for the date range
    let calendar = generateCalendar(startDate, endDate, $config.availability, $config.booked, priceInfo);


    
    $('#mmc-popup-content-inner div.body').html(calendar);


    console.log('the config...', priceInfo, $config);

}

function generateCalendar(startDate, endDate, availability, booked, priceInfo) {
    // Convert dates to Date objects if they're strings
    const start = new Date(startDate);
    const end = new Date(endDate);

    const timeDiff = Math.abs(end.getTime() - start.getTime());
    const numDays = Math.ceil(timeDiff / (1000 * 3600 * 24));


 
    let tableHTML = '<table class="calendar-table">';

    tableHTML += '<thead><tr><th>Time</th>';

    for (let i = 0; i < numDays; i++) {

        const currentDate = new Date(start);
        currentDate.setDate(start.getDate() + i);
        const formattedDate = currentDate.toLocaleDateString();

        tableHTML += `<th> ${getWeekDayFromDate(currentDate)} <br/> ${formattedDate}</th>`;
    }

    tableHTML += '</tr></thead>';
    tableHTML += '<tbody>';
    tableHTML += '<tr><td> Available </td>';


    for (let i = 0; i < numDays; i++) {

        const currentDate = new Date(start);
        currentDate.setDate(start.getDate() + i);

        if(isSitterAvailableForDate(currentDate, availability)) {
            tableHTML += `<td>${getSitterAvailabilityTimeSlots(currentDate, availability)}</td>`;
        } else {
            tableHTML += `<td> Unavailable</td>`;
        }

    }
    
    tableHTML += '</tr>';
    tableHTML += '<tr><td> Booked  </td>';

    for (let i = 0; i < numDays; i++) {


        const currentDate = new Date(start);
        currentDate.setDate(start.getDate() + i);

        tableHTML += `<td>${getSitterBookingTimeSlots(currentDate, booked)}</td>`;
    }

    tableHTML += '</tr>';


    tableHTML += getTblRowForBookNowBtn(numDays, start, priceInfo);



    tableHTML += '</tbody></table>';


    return tableHTML;
}

function getTblRowForBookNowBtn(numDays, startDate, priceInfo) {

    let htm = '';

    htm += '<tr><td> Select a time slot </td>';

    for (let i = 0; i < numDays; i++) {


        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);

        htm += `<td>`;
        htm += `<select>`;

        // priceInfo['by_idx'].forEach((item, idx) => {
        //     htm += `<option value="${idx}"> ${item.minutes} minute(s) </option>`;
        // });

        htm += `</select>`;
        htm += `<br/> <button> Book Now </button> </td>`;

    }

    htm += '</tr>';


    return htm;
    
}


function isSitterAvailableForDate(date, availabilityObj) {

    const dayName = getDayNameKey(date);
    
    const availability = availabilityObj[dayName];

    if(!availability) {
        return false;
    }

    let isHoliday = false;
    let holidayKey = 'hodidays';

    console.log(availabilityObj, 'Atiqur...3');

    if(availabilityObj[holidayKey]) {

        let ymd = formatDateYmd(date);

        availabilityObj[holidayKey].forEach(holiday => {

            console.log('Atiqur...', holiday, ymd);

            if(holiday.dates === ymd) {
                isHoliday = true;
                return false;
            }
        });
    }

    if(isHoliday) {
        return false;
    }

    return true;
}

function getWeekDayFromDate(date) {

    const day = date.getDay();

    return ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][day];
}

function formatDateYmd(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Month is 0-indexed, so add 1
    const day = String(date.getDate()).padStart(2, '0');
    
    return `${year}${month}${day}`;
}

function bookSlot(date, availability) {

    console.log(date, availability);
}

function getSitterAvailabilityTimeSlots(date, availabilityObj) {

    const dayName = getDayNameKey(date);
    
    const availability = availabilityObj[dayName];


    if(!availability) {
        return '';
    }

    let htm = '';

    availability.forEach(slot => {
        htm += `<div class="slot3">${slot.start} - ${slot.end}</div>`;
    });

    return htm;
}

function getDayNameKey(date) {

    const dayKey = {
        0: 'sunday',
        1: 'monday',
        2: 'tuesday',
        3: 'wednesday',
        4: 'thursday',
        5: 'friday',
        6: 'saturday'
    }

    const day = date.getDay();

    return dayKey[day];
}

function getSitterBookingTimeSlots(date, booked) {

    let htm = '';

    if (!booked) {
        return '';
    }

    //console.log(booked, 'Atiqur...');

    booked.forEach(slot => {
        htm += `<div class="slot3">${slot.start_time} - ${slot.end_time}</div>`;
    });

    return htm;
}

function getPriceInfoObj(priceInfo) {

    let priceInfoObj = {};

    if (priceInfo.prices) {

        priceInfoObj['time_block'] = [];
        priceInfoObj['by_idx'] = {};

        priceInfo.prices.forEach((item, idx) => {
            priceInfoObj['time_block'].push(item.minutes);
            priceInfoObj['by_idx'][idx] = item;
        });
    }

    return priceInfoObj;
}