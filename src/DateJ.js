import { format, addDays, isAfter, isBefore, parseISO, addMinutes } from 'date-fns';


function formatYmd(date) {
    return format(date, 'yyyy-MM-dd');
}

function formatYmdWithoutDash(date) {
    return format(date, 'yyyyMMdd');
}

function formatYmd2(date) {
    return format(date, 'yyyyMMdd');
}

function getDayFromDate(date) {
    return format(date, 'EEE');
}

function formatYmdHms(date) {
    return format(date, 'yyyy-MM-dd HH:mm');
}

function formatHmA(date) {
    return format(date, 'HH:mm a');
}

function makeKeyFromDates(date1, date2) {
    return format(date1, 'yyyyMMddHHmm') + '_' + format(date2, 'yyyyMMddHHmm');
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

function addMinutesToDate(date, minutes) {
    return addMinutes(date, minutes);
}

function getTimeFromKey(key) {

    const times = key.split('_');
    // 202503241030_202503241130

    const start = times[0].substring(8, 10) + ':' + times[0].substring(10, 12);
    const end = times[1].substring(8, 10) + ':' + times[1].substring(10, 12);

    return start + ' - ' + end;
}

function getDateFromKey(key) {
    return key.substring(0, 4);
}

function isDateStringIsEqualToDate(dateString, date) {
    return dateString.substring(0, 8) == formatYmd2(date);
}

const getYmdFromDateKey = (key) => {

    return key.substring(0, 4) + '-' + key.substring(4, 6) + '-' + key.substring(6, 8);
}

const time = () => {

    return Math.floor(Date.now() / 1000);
}

function getYmdHmsFromKey(key, offset = 0) {

    const times = key.split('_');

    return times[offset].substring(0, 4) + '-' + times[offset].substring(4, 6) + '-' + times[offset].substring(6, 8) + ' ' + times[offset].substring(8, 10) + ':' + times[offset].substring(10, 12) + ':00';
}


const timestampFromKey = (key, offset = 0) => {

    let dt = new Date(getYmdHmsFromKey(key, offset));

    return Math.floor(dt.getTime() / 1000);
}

export default {
    
}

export {
    formatYmd,
    formatYmdWithoutDash,
    formatYmd2,
    getDayFromDate,
    getDayNameKey,
    addMinutesToDate,
    formatYmdHms,
    makeKeyFromDates,
    formatHmA,
    getTimeFromKey,
    getDateFromKey,
    isDateStringIsEqualToDate,
    getYmdFromDateKey,
    time,
    timestampFromKey,
    getYmdHmsFromKey
}