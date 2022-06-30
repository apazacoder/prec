<template>
  <div>
    <div class="notification component-notification"
         :class="{
                    'is-visible': notification.visible,
                    'is-danger': notification.type == 'error',
                    'is-info': notification.type == 'normal',
                    'is-success': notification.type == 'success'}"
    >
      <button class="delete" @click="notification.visible=false"></button>
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
      <div class="columns is-mobile">
        <div class="column is-hidden-mobile is-one-fifth-tablet">
          <h2 v-text="itemNaming.plural"></h2>
        </div>
        <div class="column is-three-fifths-mobile is-three-fifths-tablet">
          <div class="columns is-mobile">
            <div class="column is-three-fifths-mobile">
              <div class="field is-horizontal">
                <div class="field-label is-normal is-hidden-mobile">
                  <label class="label">Buscar: </label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <p class="control">
                      <input class="input is-small" type="text" v-model="term"
                             @keyup="setFilter()"
                      >
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div class="column is-one-fifth-mobile">
              <button class="button is-active" @click="removeFilter()"
                      :class="processing ? 'is-loading' : ''"
              >
                <span class="mdi mdi-filter-remove-outline"></span>
              </button>
            </div>
          </div>
        </div>

        <div class="column right-align is-one-fifth-tablet">
          <button class="button is-active is-success" @click="createItem()">
            <span class="mdi mdi-plus"></span><span>Crear <span v-text="itemNaming.singular"></span></span>
          </button>
        </div>
      </div>
      <div class="columns table-wrapper">
        <table
            class="table crud-table customers-table is-bordered is-striped is-narrow is-hoverable is-fullwidth table-customers"
            :class="processing ? 'is-loading': ''"
        >
          <thead>
          <tr>
            <th v-for="header in headers" v-text="header.text"></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, cindex) in items">
            <td v-for="header in headers">
                            <span v-if="header.value != 'actions'">
                                <span v-if="header.hasOwnProperty('alias')"
                                      v-text="header.alias[item[header.value]]"
                                >
                                </span>
                                <span v-else v-text="item[header.value]"></span>
                            </span>
                            <span v-else>
                                  <button class="button" @click="showPlans(item)">
                                      <span class="icon mdi mdi-table"></span>
                                  </button>
                                  <button class="button" @click="showAnswers(item)">
                                      <span class="icon mdi mdi-account-question"></span>
                                  </button>
                                  <button class="button" @click="editItem(item)">
                                      <span class="icon mdi mdi-pencil"></span>
                                  </button>
                                  <button class="button" @click="confirmDelete(item)">
                                      <span class="icon mdi mdi-delete"></span>
                                  </button>
                            </span>
            </td>
          </tr>
          <tr v-if="items.length == 0">
            <td :colspan="headers.length-1">Sin datos</td>
            <td>
              <button class="button" disabled></button>
            </td>
          </tr>
          </tbody>
          <tfoot>
          <tr>
            <td :colspan="headers.length">
              <button class="button is-light"
                      :disabled="processing || parseInt(currentPage) === 1"
                      @click="setPage(currentPage - 1)"
              >Anterior
              </button>
              <button v-for="page in indexPages" v-text="page"
                      class="button"
                      :class="[( parseInt(currentPage) === parseInt(page) )? 'active' : 'is-light']"
                      :disabled="processing || page === currentPage || page === '...'"
                      @click="setPage(page)"
              ></button>
              <button class="button is-light"
                      :disabled="processing || parseInt(currentPage) ===  parseInt(pagesNumber)"
                      @click="setPage(currentPage + 1)"
              >Siguiente
              </button>
              <div class="is-hidden-mobile">
                        <span v-if="term.length >= 3">
                            Mostrando <span v-text="itemsNumber"></span> resultados que contienen: <span
                            v-text="term" class="bold"></span>
                        </span>
                <span v-else>
                        Mostrando
                            <span v-text="itemsNumber"></span>&nbsp;
                            <span v-text="itemNaming.plural"></span>
                            en <span v-text="pagesNumber"></span> páginas
                        </span>
              </div>
            </td>
          </tr>
          </tfoot>
        </table>
      </div>

      <div class="modal" :class="dialog ? 'is-active':''">
        <div class="modal-background"></div>
        <div class="modal-card">
          <header class="modal-card-head">
            <p class="modal-card-title" v-text="formTitle"></p>
            <button class="delete" aria-label="close" @click="close()"></button>
          </header>
          <section class="modal-card-body">
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Categoría</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <div class="control">
                        <div class="">
                          <select v-model="editedItem.id_category"
                                  :disabled="editedIndex==-2"
                          >
                            <option v-for="category in categories"
                                    v-text="category.name"
                                    :value="category.id"
                            >
                            </option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Nombre del cliente</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control">
                        <input class="input" ref="autofocus" type="text"
                               v-model="editedItem.fullname" :disabled="editedIndex==-2"
                               @keypress.enter="save()"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">E-mail</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control">
                        <input class="input" type="text"
                               v-model="editedItem.email" :disabled="editedIndex==-2"
                               @keypress.enter="save()"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Teléfono</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control">
                        <input class="input" type="text"
                               v-model="editedItem.phone" :disabled="editedIndex==-2"
                               @keypress.enter="save()"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Suscripción:</label>
                  </div>
                  <div class="field-body">
                    <div class="field">
                      <p class="control">
                        <input type="checkbox"
                               v-model="editedItem.newsletter_subscription"
                               :disabled="editedIndex==-2"
                               true-value="1"
                               false-value="0"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Notificaciones:</label>
                  </div>
                  <div class="field-body">
                    <div class="field field-checkbox">
                      <p class="control">
                        <input type="checkbox"

                               v-model="editedItem.notifications"
                               :disabled="editedIndex==-2"
                               true-value="1"
                               false-value="0"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="columns">
              <div class="column">
                <div class="field is-horizontal">
                  <div class="field-label is-normal">
                    <label class="label">Contacto realizado</label>
                  </div>
                  <div class="field-body">
                    <div class="field field-checkbox">
                      <p class="control">
                        <input type="checkbox"

                               v-model="editedItem.is_contact_request_processed"
                               :disabled="editedIndex==-2"
                               true-value="1"
                               false-value="0"
                        >
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <footer class="modal-card-foot">
            <div v-if="errors.length > 0" class="notification is-danger">
              <div v-for="error in errors">
                <span class="mdi mdi-alert"></span>&nbsp;&nbsp;
                <span v-text="error"></span>
              </div>
            </div>
            <progress class="progress"
                      :class="processing ? 'is-processing': 'not-processing'"></progress>
            <button v-if="editedIndex==-2" class="button is-danger is-active" @click="deleteItem()"

                    :class="processing ? 'is-loading' : ''">Si, borrar
            </button>
            <button v-else class="button is-success is-active" @click="save()"
                    :class="processing ? 'is-loading' : ''">Guardar
            </button>
            <button class="button" @click="close()">Cancelar</button>
          </footer>
        </div>
      </div>

      <div class="modal plans-modal" :class="plansDialog ? 'is-active':''">
        <div class="modal-background"></div>
        <div class="modal-card">
          <header class="modal-card-head">
            <p class="modal-card-title" v-text="plansTitle"></p>
            <button class="delete" aria-label="close" @click="closePlans()"></button>
          </header>
          <section class="modal-card-body">
<!--            <step-four :categories="plansCategories" :plans="plansPlans" :is_frontend="false"></step-four>-->
          </section>
          <footer class="modal-card-foot">

            <button class="button" @click="closePlans()">Cerrar</button>
          </footer>
        </div>
      </div>

      <div class="modal answers-modal" :class="answersDialog ? 'is-active':''">
        <div class="modal-background"></div>
        <div class="modal-card">
          <header class="modal-card-head">
            <p class="modal-card-title" v-text="answersTitle"></p>
            <button class="delete" aria-label="close" @click="closeAnswers()"></button>
          </header>
          <section class="modal-card-body">
            <dl v-for="qa in questions_and_answers">
              <dt v-text="qa.text"></dt>
              <dd v-text="qa.value =='1' ? 'SI' : 'NO'"></dd>
            </dl>
          </section>
          <footer class="modal-card-foot">

            <button class="button" @click="closeAnswers()">Cerrar</button>
          </footer>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
// import StepFour from './../../../cotizador-frontend/src/components/StepFour.vue';

export default {
  name: "Customers",
  props: ['pendings'],
  components: {
    // 'step-four': StepFour
  },
  data() {
    return {

      dialog: false,
      headers: [
        {text: 'ID', value: 'id'},
        {text: 'Nombre del cliente', value: 'fullname'},
        {text: 'E-mail', value: 'email'},
        {text: 'Teléfono', value: 'phone'},
        {
          text: 'Suscripción', value: 'newsletter_subscription', alias: {
            '0': 'NO',
            '1': 'SI'
          }
        },
        {
          text: 'Notificaciones', value: 'notifications', alias: {
            '0': 'NO',
            '1': 'SI'
          }
        },
        {
          text: 'Solicita promotor', value: 'is_contact_requested', alias: {
            '0': 'NO',
            '1': 'SI'
          }
        },
        {
          text: 'Contacto realizado', value: 'is_contact_request_processed', alias: {
            '0': 'NO',
            '1': 'SI'
          }
        },
        {text: 'Creado el', value: 'created_at'},
        {text: 'Actualizado el', value: 'updated_at'},
        {text: 'Categoría', value: 'category'},
        {text: 'Acciones', value: 'actions'},
      ],
      items: [],

      // pagination and filtering
      term: '',
      length: 10,
      page: 1,
      filterTimer: null,

      itemsNumber: 0,
      pagesNumber: 0,
      currentPage: 0,
      indexPages: [],

      // new, delete and edition
      editedIndex: -1, // -1 for new item, -2 for deleting item
      editedItem: {
        id: '',
        fullname: '',
        email: '',
        phone: '',
        newsletter_subscription: '',
        notifications: '',
        is_contact_requested: '',
        created_at: '',
        updated_at: '',
        id_category: '',
        category: ''
      },
      defaultItem: {
        id: '',
        fullname: '',
        email: '',
        phone: '',
        newsletter_subscription: '',
        notifications: '',
        is_contact_requested: '',
        created_at: '',
        updated_at: '',
        id_category: '',
        category: ''
      },

      // related data
      categories: [],

      // plans table
      plansDialog: false,
      plansTitle: '',
      plansPlans: [],
      plansCategories: [],

      // answers
      answersDialog: false,
      answersTitle: '',
      questions_and_answers: [],

      // UI
      itemNaming: {
        singular: 'Cliente',
        plural: 'Clientes'
      },
      processing: false,
      errors: [],
      notification: {
        visible: false,
        text: '',
        type: '', // 'error', 'success', 'normal',
        timer: null,
        duration: 4000
      },
    }
  },

  computed: {
    formTitle() {
      switch (this.editedIndex) {
        case -1:
          return `Crear ${this.itemNaming.singular}`;
        case -2:
          return `¿Borrar ${this.itemNaming.singular}?`;
        default:
          return `Editar ${this.itemNaming.singular}`;
      }
    },
  },
  watch: {
    dialog() {
      if (this.dialog == true) {
        this.$nextTick(() => {
          this.$refs.autofocus.focus();
        });
      }
    }
  },
  mounted() {
    this.$nextTick(() => {
      this.setOptimalRowHeight();
      this.initialize();

    });
  },
  methods: {
    initialize() {
      this.getItems(!!this.pendings)
    },
    createItem() {
      this.editedItem = Object.assign({}, this.defaultItem)
      this.dialog = true;
    },
    editItem(item) {
      this.editedIndex = this.items.indexOf(item)
      this.editedItem = Object.assign({}, item)
      this.dialog = true;
    },
    confirmDelete(item) {
      this.editedIndex = -2; //code for deleting
      this.editedItem = Object.assign({}, item)
      this.dialog = true;
    },
    showPlans(item) {
      this.processing = true;
      this.plansTitle = `Planes recomendados para ${item.fullname}`;
      axiosGetApi(`/customer_recommended_plans/${item.id}`, res => {
        this.plansDialog = true;
        this.plansPlans = res.data;
        this.processing = false;
      });
    },
    closePlans() {
      this.plansDialog = false;
    },
    showAnswers(item){
      this.processing = true;
      this.answersTitle = `Respuestas de ${item.fullname}`;
      axiosGetApi(`/customer_questions_and_answers/${item.id}`, res => {
        this.answersDialog = true;
        this.questions_and_answers = res.data;
        this.processing = false;
      });
    },
    closeAnswers() {
      this.answersDialog = false;
    },
    close() {
      this.dialog = false
      this.$nextTick(() => {
        this.item = Object.assign({}, this.defaultItem)
        this.editedIndex = -1,
            this.errors = []
      })
    },
    save() {
      if (this.editedIndex > -1) {
        this.updateItem();
      } else {
        this.postItem();
      }
    },
    getItems(with_pendings = false) {
      this.processing = true;
      axiosPostApi('/init_customers',
          {
            term: this.term,
            length: this.length,
            page: this.page
          }, res => {
            this.items = res.data.customers.itemsRows;
            this.categories = res.data.categories;
            this.itemsNumber = res.data.customers.itemsNumber;
            this.pagesNumber = res.data.customers.pagesNumber;
            this.currentPage = res.data.customers.currentPage;
            this.setFooterPages();

            // solicitudes pendientes
            if (with_pendings){
              this.term = 'pendientes';
              this.setFilter();
            }
          }, () => {
            this.processing = false;
          });

    },
    postItem() {
      this.processing = true;
      axiosPostApi('/customer', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification("No se pudo guardar", "error");
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Guardado correctamente', 'success');
        }
      }, () => {
        this.processing = false;
      })
    },
    updateItem() {
      this.processing = true;
      axiosPutApi('/customer', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification('No se pudo actualizar', 'error');
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Actualizado correctamente', 'success');
        }
      }, () => {
        this.processing = false;
      });
    },
    deleteItem() {
      this.processing = true;
      axiosDeleteApi('/customer', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification('No se pudo borrar', 'error');
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Borrado correctamente', 'success');
        }
      }, () => {
        this.processing = false;
      });
    },
    showNotification(text, type) {
      this.notification.visible = true;
      this.notification.text = text;
      this.notification.type = type;
      clearTimeout(this.notification.timer);
      this.notification.timer = setTimeout(() => {
        this.notification.visible = false;
      }, this.notification.duration);
    },
    setFooterPages() {
      this.pagesNumber = parseInt(this.pagesNumber);
      this.currentPage = parseInt(this.currentPage);
      this.indexPages = [];
      if (this.pagesNumber <= 7) {
        this.indexPages = this.pagesNumber;
      } else {
        // initial block (between the four first pages)
        if (this.currentPage <= 4) {
          this.indexPages = [1, 2, 3, 4, 5, '...', this.pagesNumber];
        }
        // final block (between the four final pages)
        else if (this.currentPage >= this.pagesNumber - 3) {
          this.indexPages = [1, '...', this.pagesNumber - 4, this.pagesNumber - 3, this.pagesNumber - 2, this.pagesNumber - 1, this.pagesNumber];
        } else {
          // medium block
          this.indexPages = [1, '...', this.currentPage - 1, this.currentPage, this.currentPage + 1, '...', this.pagesNumber];
        }
      }
    },
    setPage: function (pageNumber) {
      this.page = pageNumber;
      this.getItems();
    },
    setFilter: function () {
      clearTimeout(this.filterTimer);
      this.filterTimer = setTimeout(() => {
        this.setPage(1);
      }, 500);
    },
    removeFilter: function () {
      this.currentPage = 1;
      this.term = '';
      this.getItems();
    },
    setOptimalRowHeight() {
      // get window height - header height - space (top offset)
      let topOffset = window.getOffset(document.querySelector(".crud-table tbody")).top;
      let rowHeight = document.querySelector(".crud-table tbody td").offsetHeight;
      let documentHeight = document.querySelector("html").offsetHeight;
      let bottomOffset = document.querySelector(".crud-table tfoot").offsetHeight;
      this.length = Math.floor((documentHeight - topOffset - bottomOffset) / rowHeight);
    },
  }
}
</script>

<style scoped lang="scss">
.plans-modal {
  .modal-card {
    width: 85%;
  }
}
dl{
  margin-bottom:5px;
  dd{
    font-weight: bold;
  }
}
</style>
