<template>
    <div class="card step-card">
        <progress-bar :is-loading="processing"></progress-bar>
        <div class="mini-question" v-for="question in questions">
            <div class="mq-text" v-text="question.text"></div>
            <div class="mq-answer">
                <label>
                    <input type="radio" value="1" v-model="question.answer" @change="clearErrors">SI
                </label>
            </div>
            <div class="mq-answer">
                <label>
                    <input type="radio" value="0" v-model="question.answer" @change="clearErrors">NO
                </label>
            </div>
            <div class=" clear"></div>
        </div>
        <div class="errors" v-if="errors.length > 0">
            <div class="error color--pumpkin" v-for="error in errors" v-text="error"></div>
        </div>
        <button class="btn--raised btn--green btn-selector"
                @click="completeTwo(3)"
        >Siguiente
        </button>
    </div>
</template>

<script>
    export default {
        name: "StepTwo",
        props: ["questions"], // is linked so it's modified in the main.js file also
        data() {
            return {
                processing: false,
                answers: [],
                errors: []
            }
        },
        mounted: function () {

        },
        methods: {
            completeTwo(stepNumber) {
                this.clearErrors();
                let answers = 0;
                for (let index in this.questions) {
                    if (this.questions[index].hasOwnProperty("answer")) {
                        answers++;
                    }
                }

                if (answers == this.questions.length) {
                    this.$emit("evcompletetwo", stepNumber);
                } else {
                    this.errors.push("Por favor responda todas las preguntas");
                }

            },
            clearErrors() {
                this.errors = [];
            }
        }
    }
</script>

<style scoped>

</style>
