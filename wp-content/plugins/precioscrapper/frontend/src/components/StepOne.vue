<template>
    <div class="card step-card">
        <progress-bar :is-loading="processing"></progress-bar>
        <p class="question">
        </p>

        <div v-for="category in categories">
            <button class="btn--raised btn--green btn-selector"
                    @click="completeOne(2, category.id)"
            >
                <span v-text="category.question"></span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StepOne",
        props: ["categories", "processing"],
        data() {
            return {}
        },
        mounted: function () {

        },
        methods: {
            completeOne(stepNumber, id_category) {
                this.$emit("evcompleteone", stepNumber, id_category);
            }
        }
    }
</script>

<style scoped>

</style>
