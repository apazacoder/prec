<template>
  <div>
    <div class="notification component-notification"
         :class="{
                    'is-visible': notification.visible,
                    'is-danger': notification.type == 'error',
                    'is-info': notification.type == 'normal',
                    'is-success': notification.type == 'success'}"
    >
             <span class="icon mdi"
                   :class="{'mdi-alert': notification.type == 'error',
                 'mdi-information': notification.type == 'normal',
                 'mdi-check-bold': notification.type == 'success'}"
             ></span>
      <span v-text="notification.text"></span>
    </div>
    <progress class="progress"
              :class="processing ? 'is-processing': 'not-processing'">
    </progress>
    <div class="box has-background-white-bis">
      <div class="notification is-success is-light"
           v-if="contact_requests_pending_qty > 0">
        Tiene
        <router-link to="/customers/pending">
          <span v-text="contact_requests_pending_qty"> </span>
          solicitud<span v-if="contact_requests_pending_qty > 1">es</span>
          de clientes para promotores de venta
        </router-link>
      </div>
      <div v-for="category in composition" style="border: 1px solid lightgray; padding: 1rem;
border-radius: 1rem; margin-bottom: 1rem; font-size: .9rem;">
        <div>
          <b> <span v-text="category.name" style="font-size:1rem;"></span> </b>
        </div>
        <div>
          <div v-for="question in category.questions">
            <span v-text="question.text"></span>
          </div>
        </div>
        <div>
          <div v-for="plan in category.plans" style="width: 300px; float:left">
            <b><span v-text="plan.name" style="font-size:.95rem;"></span></b><br>
            US$ <span v-text="plan.total_price"></span>/mes
            <div>
              <div v-for="service in plan.services">
                <span v-text="service.priority"></span>.
                <span v-text="service.description"></span>
                <span v-if="service.is_generic == 1"> (Gen) </span>
                US$ <span v-text="service.price"></span>
              </div>
            </div>
          </div>
        </div>
        <hr>
        <div style="clear:both"></div>
      </div>
      <div v-if="composition.length > 0">
        <div class="notification is-info is-light">
          Para usar el plugin debe definir:
          <ul>
            <li><span class="bold">Categorías</span>: de usuarios</li>
            <li><span class="bold">Preguntas por categoría</span>: dentro de cada categoría existirán preguntas para
              habilitar servicios
            </li>
            <li><span class="bold">Planes por categoría</span>: una categoría puede tener varios planes</li>
            <li><span class="bold">Servicios por categoría</span>: cada categoría tendrá varios servicios</li>
            <li><span class="bold">Lógica</span>: determina los servicios a priorizar de acuerdo a las respuestas</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue'

export default {
  name: 'Home',
  data() {
    return {
      // UI
      processing: false,
      errors: [],
      notification: {
        visible: false,
        text: '',
        type: '', // 'error', 'success', 'normal',
        timer: null,
        duration: 4000
      },
      composition: [],
      contact_requests_pending_qty: 0,
    }
  },
  mounted: function () {
    console.log("mounted!!");
    this.getComposition();
  },
  methods: {
    getComposition() {
      this.processing = true;
      axiosGetApi('/composition', res => {
        this.composition = res.data;
        this.getContactRequestsPendingQty();
      }, () => {
        this.processing = false;
      });
    },
    getContactRequestsPendingQty() {
      this.processing = true;
      axiosGetApi('/contact_requests_pending_qty', res => {
        this.contact_requests_pending_qty = res.data;
      }, () => {
        this.processing = false;
      });
    }
  }
}
</script>
