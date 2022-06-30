<template>
  <div class="card step-card without-bg plans-card">
    <progress-bar :is-loading="processing" v-if="is_frontend" ></progress-bar>

    <div id="plans-wrapper">
      <div class="inner-plans only-desktop">

        <table>
          <thead>
          <tr>
            <th class="no-bottom-border"></th>
            <th v-for="plan in plans.plans"
                class="no-bottom-border"
                :class="(plan.type == 'best' ? 'best' : '') + (plan.type == 'custom' ? 'custom' : '')"
            >
              <div class="theader">
                <div v-if="plan.type == 'best'" class="ribbon best">recomendado
                </div>
                <div v-if="plan.type == 'custom'" class="ribbon custom">a tu medida
                </div>
                <div v-if="plan.type != 'best' && plan.type != 'custom'" class="ribbon hidden">.
                </div>
                <div class="name"
                     v-text="plan.type != 'custom' ? plan.name : 'Personalizado'"
                >
                </div>
              </div>
            </th>
          </tr>
          <tr>
            <th class="no-top-border"></th>
            <th v-for="plan in plans.plans"
                class="no-top-border"
                :class="(plan.type == 'best' ? 'best' : '') + (plan.type == 'custom' ? 'custom' : '')"
            >
              <div class="theader">
                <div class="price">
                  <span class="money-symbol">US$</span>
                  <span class="amount"
                        v-text="plan.total_price.toFixed(2).split('.')[0]"
                  ></span>
                  <span class="decimal-month">
                    <span class="decimal"
                          v-text="plan.total_price.toFixed(2).split('.')[1]"
                    ></span>
                    <span class="month">/mes</span>
                  </span>
                </div>
              </div>
            </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(service, si) in plans.services">
            <td class="service">
              <span v-text="service.description"></span>
              <span class="mdi mdi-information-outline tooltip-wrapper">
                <span class="inner-tooltip" v-text="service.detail"></span>
              </span>
            </td>
            <td v-for="plan in plans.plans"
                :class="(plan.type == 'best' ? 'best' : '') + (plan.type == 'custom' ? 'custom' : '')"
            >
              <span v-if="plan.services[si].is_enabled"
                    class="mdi mdi-check">
              </span>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
      <div class="inner-plans only-mobile">
        <div class="plan-card" v-for="(plan, pi) in plans.plans">
          <div class="ribbon"
               :class="[plan.type == 'best' ? 'reco' : '', plan.type == 'custom' ? 'custom' : '']">
            <span class="ribbon-text"
                  v-if="plan.type =='best'">
                            recomendado
            </span>
            <span class="ribbon-text"
                  v-if="plan.type =='custom'">
                            a tu medida
            </span>
          </div>
          <div class="name"
               v-text="plan.type != 'custom' ? plan.name : 'Personalizado'">

          </div>
          <div class="price">
            US$
            <span class="amount"
                  v-text="plan.total_price.toFixed(2).split('.')[0]">
            </span>.<span class="decimal"
                          v-text="plan.total_price.toFixed(2).split('.')[1]">
            </span><span class="month"
          >/mes
            </span>

          </div>
          <div class="services-wrapper"
               :class="(plan.type == 'best' || plan.type == 'custom') ? 'services-toggle-on': ''"
          >
            <div class="services">
              <div v-for="(service, si) in plan.services" class="service"
                   v-if="service.is_enabled"
                   v-text="service.description"
              >
              </div>
            </div>
            <div class="toggle-services">
              <span class="text-toggle-off">
                Ocultar detalles
                <span class="mdi mdi-chevron-up"></span>
              </span>
              <span class="text-toggle-on">
                Mostrar detalles
                <span class="mdi mdi-chevron-down"></span>
              </span>
            </div>
          </div>
        </div>
      </div>
      <div class="plan-actions">
        <progress-bar :is-loading="processing" class="is-info" v-if="is_frontend"></progress-bar>
        <button class="btn--raised btn--red btn" @click="getPdf()"
                :disabled="processing"
        >
          <span class="mdi mdi-file-pdf-outline"></span><span v-text="">Descargar en PDF</span>
        </button>
        <button v-if="is_frontend"
            class="btn--raised btn--primary btn" @click="requestContact()"
                :disabled="processing || contact_requested"
        >
          <span class="mdi mdi-contacts"></span><span v-text="">Solicitar promotor</span>
        </button>
        <div class="tile color--green-sea" v-if="contact_requested">
          Su solicitud ha sido envíada, uno de nuestros promotores se contactará con usted a la brevedad
        </div>
        <div class="tile color--red" v-if="contact_requested && errors.length > 0 ">
          No pudimos procesar su solicitud, intente de nuevo mas tarde por favor
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ProgressBar from './ProgressBar.vue';
export default {
  name: "StepFour",
  props: ["categories", "plans", "is_frontend"],
  components: {
    'progress-bar': ProgressBar,
  },
  data() {
    return {
      processing: false,
      errors: [],
      contact_requested: false,
      notification: {
        visible: false,
        text: '',
        type: '', // 'error', 'success', 'normal',
        timer: null,
        duration: 4000
      },
    }
  },
  mounted: function () {
    jQuery(".toggle-services").on("click", function () {
      jQuery(this).parent().toggleClass("services-toggle-on");
    });
  },
  methods: {
    actualDate() {
      let d = new Date();
      let dm = d.getMonth();
      let month = "";
      switch (dm) {
        case 0:
          month = "Enero";
          break;
        case 1:
          month = "Febrero";
          break;
        case 2:
          month = "Marzo";
          break;
        case 3:
          month = "Abril";
          break;
        case 4:
          month = "Mayo";
          break;
        case 5:
          month = "Junio";
          break;
        case 6:
          month = "Julio";
          break;
        case 7:
          month = "Agosto";
          break;
        case 8:
          month = "Septiembre";
          break;
        case 9:
          month = "Octubre";
          break;
        case 10:
          month = "Noviembre";
          break;
        case 11:
          month = "Diciembre";
          break;
      }
      let day = "";
      let dd = d.getDay();
      switch (dd) {
        case 0:
          day = "Domingo";
          break;
        case 1:
          day = "Lunes";
          break;
        case 2:
          day = "Martes";
          break;
        case 3:
          day = "Miércoles";
          break;
        case 4:
          day = "Jueves";
          break;
        case 5:
          day = "Viernes";
          break;
        case 6:
          day = "Sábado";
          break;
      }
      return day + " " + d.getDate() + " de " + month + " de " + d.getFullYear();
    },
    getPdf() {
      this.processing = true;
      if (this.plans.plans.length > 0) {
        let id_custom_plan = this.plans.plans[0].id;
        console.log("id_custom_plan: " + id_custom_plan);
        axiosGetBlobApi(`/customer_pdf/${id_custom_plan}`, res => {
          this.processing = false;
          FileDownload(res.data, `Tu cotización del ${this.actualDate()}.pdf`);
        });
      } else {
        alert('No hay planes');
      }
    },
    requestContact() {
      this.processing = true;
      if (this.plans.plans.length > 0) {
        console.log('here we go!');
        let id_custom_plan = this.plans.plans[0].id;
        console.log("id_custom_plan: " + id_custom_plan);
        axiosPostApi('/request_contact', {"id_custom_plan": id_custom_plan}, res => {
          if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
            this.errors = res.data.errors;
          } else {
            this.contact_requested = true;
            this.processing = false;
          }
        });
      }
    }
  },
  watch: {}
}
</script>

<style scoped lang="scss">
#plans-wrapper {
  color: #162D3D;
  overflow-x: auto;
  padding-bottom: 50px;
  padding-top: 20px;

  .plan, .service-enabled {
    &.custom {
      background: #F7F9Fb;
    }

    &.reco {
      background: #fafcfe;
    }
  }

  .inner-plans {
    min-width: 1000px;
    background: #fff;
    border: 1px solid #DFE5EB;

    table{
      table-layout:fixed;
      width: 100%;
      border-collapse: collapse;
      margin: 0;
      th{
        padding: 0;
      }
      thead{
        tr{
          border-bottom: none;
        }
        tr:hover{
          background: none;
        }
      }
      tr:nth-child(2n+1){
        background: none;
      }
      tbody{
        tr{
          &:hover:nth-child(2n){
            background: none;
          }
          &:hover{
            td{
              background: #F1F3F6;
            }
            background: #F1F3F6;
          }
        }

        .tooltip-wrapper {
          display: block;
          float: right;
          position: relative;

          &:hover {
            .inner-tooltip {
              visibility: visible;
              transform: scale(100%);
              color: #37474f;
            }
          }

          .inner-tooltip {
            transform-origin: 0% 50%;
            transform: scale(50%);
            visibility: hidden;
            transition: .15s ease-in;
            position: absolute;
            background: white;
            top: -1.2rem;
            width: 300px;
            left: 1.5rem;
            box-shadow: -5px 1px 25px 1px #ccc;
            z-index: 2;
            border-radius: 7px;
            padding: 10px;
            min-height: 3rem;
            font-size: .8rem;

            &:before {
              content: "";
              position: absolute;
              top: 1rem;
              left: -1.1rem;
              background: transparent;
              border-left: 10px solid transparent;
              border-right: 10px solid white;
              border-top: 10px solid transparent;
              border-bottom: 10px solid transparent;
            }
          }
        }


      }
    }
    table th, table td{
      border: 1px solid #D4DBE3;
    }
    table th, table td{
      line-height:1.2;
    }
    table th.no-top-border{
      border-top: none;
    }
    table th.no-bottom-border{
      border-bottom: none;
    }
    table th.best, table td.best{
      background: #FAFCFE;
    }

    table th.custom, table td.custom{
      background: #F7F9FB;
    }
    .theader{
      position: relative;
      text-align: center;
    }
    .theader .ribbon{
      width: 68%;
      left: 16%;
      position: relative;
      top: -14px;
      text-align: center;
      border: 1px solid #D4DBE3;
      text-transform: uppercase;
      background: #fff;
      font-size: .7rem;
      font-weight: bold;
      border-radius: 5px;
      padding: 4px 0 4px;

    }
    .theader .ribbon.hidden{
      visibility:hidden;
      color: transparent;
    }
    .theader .ribbon.custom{
      color: #0d47a1;
    }
    .theader .ribbon.best{
      color: #2979ff;
    }

    .theader .name, .theader .price {
      color: #37474f;
    }

    .theader .name {
      font-weight: bold;
    }

    .theader .price {
      padding: 15px 0 15px;
      line-height: 2rem;
    }
    .theader .price .money-symbol {
      vertical-align: top;
      line-height: .5rem;
      font-size: .85rem;
      font-weight: normal;
      padding-right: 3px;
    }

    .theader .price .amount {
      font-size: 1.6rem;
      font-weight: bold;
    }
    .theader .price .decimal-month{
      margin-left: 2px;
      font-weight: normal;
    }

    .theader .price  .month {
      font-size: .85rem;
      font-weight: normal;
    }

    tbody td:nth-child(n+2){
      text-align: center;
    }

    tbody td{
      padding: 10px 10px;
      vertical-align: middle;
    }
    tbody img{
      width: 15px;
      height: auto;
    }
    tbody .service{
      font-size: .9rem;
      color: #37474f;
    }
    tbody td:nth-child(1), thead th:nth-child(1){
      width: 30%;
    }

    // from pdf styles to html extra selectors

  }

  .plan-actions {
    text-align: center;
    padding: 0 0 10px;
    .is-info{
      position: relative;
      margin-bottom: 10px;
    }
    .btn {
      line-height: 1.3;
      font-family: "Roboto", sans-serif;
      font-size: .95rem;
      letter-spacing: .05rem;
      .mdi{
        font-size: 1.3rem;
        margin-right: .2rem;
      }
      &:nth-child(2){
        margin-right: 10px;
      }
    }
  }
}

// responsive
.inner-plans {
  &.only-desktop {
    display: block;
  }

  &.only-mobile {
    display: none;
  }
}

$textColor: #37474f;
@media only screen and (max-width: 575px) {
  #plans-wrapper {
    padding-top: 0 !important;
    .plan-actions{
      .btn{
        width: 100%;
      }
    }
  }
  .step-card {
    padding: 12px 12px 0;

    .question {
      font-size: 1rem;
    }

    .btn-selector {
      font-size: 1rem;
    }

    .mini-question {
      font-size: 1rem;
      line-height: 1.2;
    }

    // planes
    .inner-plans {
      background: none !important;
      border: none !important;
      min-width: 320px !important;

      &.only-desktop {
        display: none;
      }

      &.only-mobile {
        display: block;
      }

      .plan-card {
        background: #fff;
        padding: 18px 18px 0;
        margin-bottom: 10px;
        border: 1px solid #cfd8dc;
        border-radius: 10px;

        .ribbon {
          text-align: center;
          font-weight: bold;
          font-size: .9rem;

          &.custom, &.reco {
            text-transform: uppercase;
            margin-bottom: 6px;
            padding: 3px 0;
          }

          &.custom {
            color: #78909c;
            background: #E0E8F4;
          }

          &.reco {
            color: #3a76db;
            background: #D5E3FB;
          }
        }

        .name {
          font-weight: bold;
          font-size: 1.15rem;
          color: $textColor;
        }

        .price {
          font-weight: bold;
          font-size: 1.3rem;
          color: $textColor;

          .amount {

          }

          .decimal {

          }

          .month {
            color: $textColor;
            font-weight: normal;
            font-size: .9rem;
          }
        }

        .services-wrapper {
          .text-toggle-on {
            display: block;
          }

          .text-toggle-off {
            display: none;
          }

          .services {
            //visibility:hidden;
            height: 0;
            transform-origin: 50% 0%;
            transform: scaleY(0%);
            transition: .15s;
            overflow: hidden;

            font-size: .9rem;

            .service {
              color: $textColor;
            }
          }

          .toggle-services {
            font-size: .9rem;
            border-top: 1px solid #cfd8dc;
            padding: 10px 0;
            margin: 15px 0 0;
          }

          &.services-toggle-on {
            .services {
              transform: scaleY(100%);
              //visibility:visible;
              height: auto;
            }

            .text-toggle-on {
              display: none;
            }

            .text-toggle-off {
              display: block;
            }
          }
        }
      }
    }
  }
}

</style>
