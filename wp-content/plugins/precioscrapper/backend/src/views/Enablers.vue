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
    <div class="box has-background-white-bis enablers-wrapper">
      <!-- START CATEGORY SELECTOR -->
      <div class="columns">
        <div class="column">
          <div class="field is-horizontal">
            <div class="field-label is-normal" style="flex-grow:2">
              <label class="label">Seleccione una categoría:</label>
            </div>
            <div class="field-body">
              <div class="field">
                <div class="control">
                  <div class="">
                    <select v-model="selectedCategoryId"
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
      <!-- END CATEGORY SELECTOR-->
      <div class="columns" v-for="category in categories" v-if="selectedCategoryId == category.id">
        <div class="column">
          <div class="row">
            <div class="col-xs-12">
              <h2 class="center-align bold">Lógica de <span v-text="category.name"></span></h2>
            </div>
            <div class="col-xs-12">
              <div v-for="question in category.questions" class="question">
                <div v-for="possible_answer in question.possible_answers" class="possible-answer">
                  <div class="col-xs-12">
                    A la pregunta <span class="bold" v-text="question.text"></span>
                    ,si la respuesta es <span class="bold" v-text="possible_answer.value ? 'SI': 'NO'"></span>
                    priorizar los siguientes servicios:
                  </div>
                  <div class="col-xs-12 col-md-6 col-lg-4"
                       v-for="service in category.services"
                  >
                    <label class="checkbox"
                           :disabled="!selectedCategoryId">
                      <input type="checkbox" class="enabler-checkbox"
                             :disabled="!selectedCategoryId"
                             @change="updateEnabler(service.id, possible_answer.value, question.id)"
                             :checked="enablersIndex.indexOf(possible_answer.value+'_'+service.id+'_'+question.id) != -1"
                      >
                      <span v-text="service.description"></span>
                      <span v-if="service.is_generic=='1'">(G)</span>
                    </label>
                  </div>
                  <div class="clear"></div>
                </div>
                <div class="clear"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Enablers",
  data() {
    return {
      dialog: false,
      categories: [],
      enablers: [],

      enablersIndex: [],

      selectedCategoryId: 0,

      processing: false,
      errors: [],
      notification: {
        visible: false,
        text: '',
        type: '', // 'error', 'success', 'normal',
        timer: null,
        duration: 4000
      },

      updateTimer: null,
    }
  },

  computed: {},
  watch: {
    selectedCategoryId: function () {
      console.log("changed selectedcat!");
    },
    enablers: function () {
      let aux = [];
      for (let index in this.enablers) {
        aux.push(this.enablers[index].desired_answer + "_" + this.enablers[index].id_service + "_" + this.enablers[index].id_question);
      }
      this.enablersIndex = aux;
    }
  },
  mounted() {
    this.initialize();
  },
  methods: {
    initialize() {
      this.getItems(true);
    },
    getItems(setFirst = false) {
      this.processing = true;
      axiosGetApi('/init_enablers', res => {
        this.categories = res.data.categories;
        if (setFirst) {
          this.setFirstCategory();
        }
        this.enablers = res.data.enablers;
      }, () => {
        this.processing = false;
      });
    },
    setFirstCategoryAndQuestion() {
      this.setFirstCategory();
      this.setFirstQuestion();
    },
    setFirstCategory() {
      if (this.categories.length) {
        this.selectedCategoryId = this.categories[0].id;
      }
    },
    setFirstQuestion() {
      if (this.categories.length && this.categories[0].questions.length) {
        this.selectedQuestionId = this.categories[0].questions[0].id;
      }
    },
    updateEnabler(id_service, desired_answer, id_question) {
      let enabler = {
        id_service: id_service,
        desired_answer: desired_answer,
        id_question: id_question
      };
      this.processing = true;
      this.notification.visible = false;
      axiosPutApi('/enabler', {"item": enabler}, res => {
        if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
          this.showNotification('No se pudo guardar', 'error');
          this.errors = res.data.errors;
        } else {
          this.getItemsWithContaintment();
          this.showNotification("Guardado correctamente", 'success');
        }
        this.processing = false;
      });
    },
    getItemsWithContaintment() {
      clearTimeout(this.updateTimer);
      this.updateTimer = setTimeout(() => {
        this.getItems();
      }, 1200);
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
.question {
  padding-bottom: 1px;
  margin-bottom: 10px;
  border-radius: 8px;
  box-shadow: 0 2px 4px 0 #ccc;
}

.possible-answer {
  margin-bottom: 5px;
}
</style>
