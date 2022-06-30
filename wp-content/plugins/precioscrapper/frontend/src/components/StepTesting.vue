<template>
    <div class="card step-card step-testing">
        <progress-bar :is-loading="processing"></progress-bar>
        <div>
            <div class="container g--12 g-s--12">
                <div class="g--12 g-s--12">
                    <button class="btn--raised btn--green btn-selector"
                            @click="getPlans()"
                    >
                        <span>Obtener lista de planes</span>
                    </button>
                </div>
            </div>
            <div class="container g--12 g-s--12">
                <div class="g--6 g-s--12">
                    <div class="input-field">
                        <label>id_plan:
                            <input type="text" v-model="id_plan">
                        </label>
                    </div>
                </div>
                <div class="g--6 g-s--12">
                    <button class="btn--raised btn--green btn-selector"
                            @click="getPlanDetails()"
                    >
                        <span>Get plan details by id</span>
                    </button>
                </div>
            </div>
            <div class="container g--12 g-s--12">
                <div class="g--12 g-s--12">
                    <button class="btn--raised btn--green btn-selector"
                            @click="getLogicDetails()"
                    >
                        <span>Get logic details</span>
                    </button>
                </div>
            </div>


            <div class="container testing-results">
                <pre v-text="results"></pre>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StepTesting",
        data() {
            return {
                id_plan: '',
                results: [],

                processing: false,
                errors: []
            }
        },
        mounted: function () {

        },
        methods: {
            getPlans() {
                this.processing = true;

                axiosGetApi('/test_plans', res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
                        // show errors for the user to know them
                        console.log(this.errors);
                        this.errors = res.data.errors;
                    } else {
                        this.results = res.data.plans;
                        console.log(this.results);
                    }
                }, () => {
                    this.processing = false;
                });
            },
            getPlanDetails() {
                this.processing = true;
                axiosPostApi('/test_plan_details', {"id_plan": this.id_plan}, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
                        // show errors for the user to know them
                        console.log(this.errors);
                        this.errors = res.data.errors;
                    } else {
                        this.results = res.data.plan_details;
                        console.log(this.results);
                    }
                }, () => {
                    this.processing = false;
                });
            },
            getLogicDetails(){
                this.processing = true;

                axiosGetApi('/test_logic_details', res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
                        // show errors for the user to know them
                        console.log(this.errors);
                        this.errors = res.data.errors;
                    } else {
                        this.results = res.data.logic_details;
                        console.log(this.results);
                    }
                }, () => {
                    this.processing = false;
                });
            }

        }
    }
</script>

<style scoped>
    .g--12, .g--6 {
        margin-top: 10px !important;
        margin-bottom: 10px !important;
    }
    .testing-results{
        font-family: monospace !important;
        font-size: .75rem !important;
        color: #000 !important;
    }
</style>
