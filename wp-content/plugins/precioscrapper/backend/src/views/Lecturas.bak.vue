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
      <div class="columns">
        <div class="column is-hidden-mobile">
          <h2 v-text="itemNaming.plural"></h2>
        </div>
        <div class="column right-align">
          <button class="button is-active is-success" @click="createItem()">
            <span class="mdi mdi-plus"></span><span>Crear <span v-text="itemNaming.singular"></span></span>
          </button>
        </div>
      </div>
      <div class="columns">
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
                                  <button class="button" @click="editItem(item)">
                                      <span class="icon mdi mdi-pencil"></span>
                                  </button>
                                  <button class="button" @click="confirmDelete(item)">
                                      <span class="icon mdi mdi-delete"></span>
                                  </button>
                            </span>
            </td>
          </tr>
          </tbody>
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
    </div>
  </div>
</template>

<script>
export default {
  name: "Lecturas",
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
  created() {
    this.initialize()
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
    getItems() {
      this.processing = true;
      axiosGetApi('/lecturas', res => {
        this.items = res.data;
      }, () => {
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
      }, () => {
        this.processing = false;
      })
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
      }, () => {
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
    }
  }
}
</script>

<style scoped>

</style>
