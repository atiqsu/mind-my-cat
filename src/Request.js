
const request = function (method, route, data = {}) {

    let obj = window.mmcFrontObj || window.mmcAdminObj;
    let url = obj.ajax_url ;
    
    if ( route ) {

        url = `${obj.rest.url}/${route}`;
    }
    
    const headers = {'X-WP-Nonce': obj.rest.nonce};

    data.q_t = Date.now();

    return new Promise((resolve, reject) => {

        window.jQuery.ajax({
            url: url,
            type: method,
            data: data,
            dataType: 'json',
            headers: headers
        })
        .then(response => resolve(response))
        .fail(errors => reject(errors.responseJSON));
    });
}


export default {
    get(route, data = {}) {
        return request('GET', route, data);
    },
    post(route, data = {}) {
        return request('POST', route, data);
    }
};
