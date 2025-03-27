

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


const getTimeSlotFromPriceInfo = (priceInfo) => {

    let priceInfoObj = [];

    if (priceInfo.prices) {

        priceInfo.prices.forEach((item, idx) => {
            priceInfoObj.push(item.minutes);
        });
    }

    return priceInfoObj;
}



export default { 

};

export { 
    getTimeSlotFromPriceInfo,
    getPriceInfoObj,
 };
