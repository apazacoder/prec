// import styles and scripts
import sassStyles from './css/style.scss';
import './js/script';
import Vue from 'vue'

Vue.config.productionTip = false
document.addEventListener("DOMContentLoaded", function (event) {
    initAdminApp();
});

import StepTesting from './components/StepTesting';
import StepOne from './components/StepOne';
import StepTwo from './components/StepTwo';
import StepThree from './components/StepThree';
// import StepFour from './components/StepFour';
import ProgressBar from './components/ProgressBar';

import TablaInfaoliva from './components/TablaInfaoliva';
import TablaPoolred from './components/TablaPoolred';
import TablaAlmazaras from './components/TablaAlmazaras';

import TablaHistoricoPreciosInfaoliva from './components/TablaHistoricoPreciosInfaoliva';
import TablaHistoricoPreciosPoolred from './components/TablaHistoricoPreciosPoolred';
import TablaHistoricoPreciosAlmazaras from './components/TablaHistoricoPreciosAlmazaras';

import TablaHistoricoTiposExtraVirgen from './components/TablaHistoricoTiposExtraVirgen';
import TablaHistoricoTiposVirgen from './components/TablaHistoricoTiposVirgen';
import TablaHistoricoTiposLampante from './components/TablaHistoricoTiposLampante';

import GraficoHistoricoPreciosInfaoliva from './components/GraficoHistoricoPreciosInfaoliva';

Vue.component('step-testing', StepTesting);
Vue.component('step-one', StepOne);
Vue.component('step-two', StepTwo);
Vue.component('step-three', StepThree);
// Vue.component('step-four', StepFour);
Vue.component('progress-bar', ProgressBar);

Vue.component('tabla-infaoliva', TablaInfaoliva);
Vue.component('tabla-poolred', TablaPoolred);
Vue.component('tabla-almazaras', TablaAlmazaras);

Vue.component('tabla-historico-precios-infaoliva', TablaHistoricoPreciosInfaoliva);
Vue.component('tabla-historico-precios-poolred', TablaHistoricoPreciosPoolred);
Vue.component('tabla-historico-precios-almazaras', TablaHistoricoPreciosAlmazaras);

Vue.component('tabla-historico-tipos-extra-virgen', TablaHistoricoTiposExtraVirgen);
Vue.component('tabla-historico-tipos-virgen', TablaHistoricoTiposVirgen);
Vue.component('tabla-historico-tipos-lampante', TablaHistoricoTiposLampante);

Vue.component('grafico-historico-precios-infaoliva', GraficoHistoricoPreciosInfaoliva);

function initAdminApp() {
    let shortcodes = [
        '#scrapper-app',
        '#scrapper-tabla-poolred',
        '#scrapper-tabla-infaoliva',
        '#scrapper-tabla-almazaras',
        '#scrapper-tabla-historico-precios-poolred',
        '#scrapper-tabla-historico-precios-infaoliva',
        '#scrapper-tabla-historico-precios-almazaras',
        '#scrapper-tabla-historico-tipos-extra-virgen',
        '#scrapper-tabla-historico-tipos-virgen',
        '#scrapper-tabla-historico-tipos-lampante',
    ];

    // start all vue instances if the element exists in the page
    for (let si in shortcodes){
        if (document.querySelector(shortcodes[si])) {
            new Vue({ el: shortcodes[si] });
        }
    }

    // section the shortcodes


}
