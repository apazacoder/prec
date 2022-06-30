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
              <button class="button is-active btn-remove-filter" @click="removeFilter()"
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
        <table class="table crud-table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
          <thead>
          <tr>
            <th v-for="header in headers" v-text="header.text"></th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="(item, cindex) in items">
            <td v-for="header in headers">
              <span v-if="header.value != 'actions'"
                    v-text="item[header.value]">
              </span>
              <span v-else>
                  <button class="button is-info is-outlined" @click="editItem(item)">
                      <span class="icon mdi mdi-pencil"></span>
                  </button>
                  <button class="button is-danger is-outlined" @click="confirmDelete(item)">
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
      <div class="modal modal-customer-foods" :class="dialog ? 'is-active':''">
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
                  <label class="label">Leído el</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <p class="control">
                      <input class="input" ref="autofocus" type="text"
                             v-model="editedItem.leido_el" :disabled="editedIndex==-2"
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
                  <label class="label">Código de respuesta</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <p class="control">
                      <input class="input" type="text"
                             v-model="editedItem.codigo_respuesta" :disabled="editedIndex==-2"
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
                  <label class="label">Resultado</label>
                </div>
                <div class="field-body">
                  <div class="field">
                    <p class="control">
                      <input class="input" type="text"
                             v-model="editedItem.resultado" :disabled="editedIndex==-2"
                             @keypress.enter="save()"
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

            <div class="columns">
              <div class="column">
                <button v-if="editedIndex==-2" class="button is-danger is-fullwidth is-active" @click="deleteItem()"

                        :class="processing ? 'is-loading' : ''">Si, borrar
                </button>
                <button v-else class="button is-fullwidth is-success is-active" @click="save()"
                        :class="processing ? 'is-loading' : ''">Guardar
                </button>
              </div>
              <div class="column">
                <button class="button is-fullwidth" @click="close()">Cancelar</button>
              </div>
            </div>
          </footer>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
// @ is an alias to /src
// import HelloWorld from '@/components/HelloWorld.vue'


export default {
  name: 'Lecturas',
  data() {
    return {
      dialog: false,
      headers: [
        {text: 'ID', value: 'id'},
        {text: 'Leído el', value: 'leido_el'},
        {text: 'Código de respuesta', value: 'codigo_respuesta'},
        {text: 'Resultado', value: 'resultado'},
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
        leido_el: '',
        codigo_respuesta: '',
      },
      defaultItem: {
        id: '',
        leido_el: '',
        codigo_respuesta: '',
      },

      // UI
      itemNaming: {
        singular: 'Lectura',
        plural: 'Lecturas'
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
  mounted: function () {
    this.$nextTick(() => {
      this.setOptimalRowHeight();
      this.initialize();
    });
  },
  methods: {
    initialize() {
      this.getItems();
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
    close() {
      this.dialog = false;
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
    getItems() {
      this.processing = true;
      axiosPostApi('/init_lecturas',
          {
            term: this.term,
            length: this.length,
            page: this.page
          },
          res => {
            this.items = res.data.lecturas.itemsRows;
            this.itemsNumber = res.data.lecturas.itemsNumber;
            this.pagesNumber = res.data.lecturas.pagesNumber;
            this.currentPage = res.data.lecturas.currentPage;
            this.setFooterPages();
            this.processing = false;
          });
    },
    postItem() {
      this.processing = true;
      axiosPostApi('/lectura', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification("No se pudo guardar", "error");
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Guardado correctamente', 'success');
        }
        this.processing = false;
      });
    },
    updateItem() {
      this.processing = true;
      axiosPutApi('/lectura', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification('No se pudo actualizar', 'error');
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Actualizado correctamente', 'success');
        }
        this.processing = false;
      });
    },
    deleteItem() {
      this.processing = true;
      axiosDeleteApi('/lectura', {"item": this.editedItem}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification('No se pudo borrar', 'error');
          this.errors = res.data.errors;
        } else {
          this.close();
          this.getItems();
          this.showNotification('Borrado correctamente', 'success');
        }
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
      let topOffset = window.getOffset(document.querySelector(".crud-table tbody")).top + 30;
      let rowHeight = document.querySelector(".crud-table tbody td").offsetHeight;
      let documentHeight = document.querySelector("html").offsetHeight;
      let bottomOffset = document.querySelector(".crud-table tfoot").offsetHeight;
      this.length = Math.floor((documentHeight - topOffset - bottomOffset) / rowHeight);
    },
  }
}
</script>
