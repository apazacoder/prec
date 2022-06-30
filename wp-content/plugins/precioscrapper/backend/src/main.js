// import styles and scripts
import sassStyles from './css/style.scss';
import './js/script';

import Vue from 'vue'
import router from './router'
Vue.config.productionTip = false

document.addEventListener("DOMContentLoaded", function (event) {
    initAdminApp();
});

function initAdminApp() {
    if (document.querySelector("#backend")) {
        new Vue({
            router,
            data() {
                return {
                    message: 'Kaguabongadd!'
                }
            },
            el: '#backend'
        });
    }
}
