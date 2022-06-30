// wrapper for axios
import Axios from './axios.min';
window.axios = Axios;

window.axiosGeneric = function (url, method, data, callback, finallyCallback = null) {
    axios({
        method: method,
        url: url,
        data: data
    })
        .then(res => {
            callback(res);
        })
        .catch(error => {
            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            } else if (error.request) {
                // The request was made but no response was received
                // `error.request` is an instance of XMLHttpRequest  in the browser and an instance of
                // http.ClientRequest in node.js
                console.log(error.request);
            } else {
                // Something happened in setting up the request that triggered an Error
                console.log('ERROR in call to ' + url + ' with ' + method, error.message);
            }
            console.log(error.config);
        })
        .then(() => {
            if (finallyCallback && typeof (finallyCallback) === "function") {
                finallyCallback();
            }
        });
};

// Wrappers for any url call
window.axiosGet = function (url, callback, finallyCallback = null) {
    axiosGeneric(url, 'get', Date.now(), callback, finallyCallback);
};

window.axiosPost = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(url, 'post', data, callback, finallyCallback);
};

window.axiosPut = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(url, 'put', data, callback, finallyCallback);
};

window.axiosDelete = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(url, 'delete', data, callback, finallyCallback);
};

const API_URL = '/wp-json/cs/v1'

// Wrappers for any url call
window.axiosGetApi = function (url, callback, finallyCallback = null) {
    axiosGeneric(`${API_URL}${url}`, 'get', Date.now(), callback, finallyCallback);
};

window.axiosPostApi = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(`${API_URL}${url}`, 'post', data, callback, finallyCallback);
};

window.axiosPutApi = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(`${API_URL}${url}`, 'put', data, callback, finallyCallback);
};

window.axiosDeleteApi = function (url, data, callback, finallyCallback = null) {
    axiosGeneric(`${API_URL}${url}`, 'delete', data, callback, finallyCallback);
};

window.axiosGenericBlob = function (url, method, data, callback, finallyCallback = null) {
    axios({
        method: method,
        url: url,
        data: data,
        responseType: 'blob',
        // headers: {'Content-Disposition': 'attachment'}
    })
        .then(res => {
            callback(res);
        })
        .catch(error => {
            if (error.response) {
                // The request was made and the server responded with a status code
                // that falls out of the range of 2xx
                console.log(error.response.data);
                console.log(error.response.status);
                console.log(error.response.headers);
            } else if (error.request) {
                // The request was made but no response was received
                // `error.request` is an instance of XMLHttpRequest  in the browser and an instance of
                // http.ClientRequest in node.js
                console.log(error.request);
            } else {
                // Something happened in setting up the request that triggered an Error
                console.log('BLOB ERROR in call to ' + url + ' with ' + method, error.message);
            }
            console.log(error.config);
        })
        .then(() => {
            if (finallyCallback && typeof (finallyCallback) === "function") {
                finallyCallback();
            }
        });
};

window.axiosGetBlob = function (url, callback, finallyCallback = null) {
    axiosGenericBlob(url, 'get', Date.now(), callback, finallyCallback);
};

window.axiosPostBlob = function (url, data, callback, finallyCallback = null) {
    axiosGenericBlob(url, 'post', data, callback, finallyCallback);
};

window.axiosGetBlobApi = function (url, callback, finallyCallback = null) {
    axiosGenericBlob(`${API_URL}${url}`, 'get', Date.now(), callback, finallyCallback);
};

window.axiosPostBlobApi = function (url, data, callback, finallyCallback = null) {
    axiosGenericBlob(`${API_URL}${url}`, 'post', data, callback, finallyCallback);
};
