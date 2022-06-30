<template>
    <div class="card step-card">
        <progress-bar :is-loading="processing"></progress-bar>
        <div class="question">
            Después de completar el formulario podrá ver su cotización
        </div>
        <div class="fields">
            <div class="input-field">
                <label>Nombre y apellido:
                    <input type="text" v-model="customer.fullname" @keyup="clearErrors"
                           ref="fullname"
                           autofocus
                    >
                </label>
            </div>
            <div class="input-field half">
                <label>E-mail:
                    <input type="text" v-model="customer.email" @keyup="clearErrors">
                </label>
            </div>
            <div class="input-field half">
                <label>Tel. de contacto:
                    <input type="text" v-model="customer.phone" @keyup="clearErrors"
                           @keypress.enter="completeThree()"
                    >
                </label>
            </div>
            <div class="clear"></div>
            <div class="checkboxes">
                <div class="checkbox-field half">
                    <label>
                        <input type="checkbox" v-model="customer.newsletter_subscription">
                        <span>Quiero suscribirme al newsletter</span>
                    </label>
                </div>
                <div class="checkbox-field half">
                    <label>
                        <input type="checkbox" v-model="customer.notifications">
                        <span>Acepto recibir notificaciones de Zynerdata</span>
                    </label>
                </div>
                <div class="clear"></div>
            </div>
            <div class="errors" v-if="errors.length > 0">
                <div class="error color--pumpkin" v-for="error in errors" v-text="error"></div>
            </div>
            <button class="btn--raised btn--green btn-selector"
                    @click="completeThree()"
            >Ver cotización
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StepThree",
        props: ["categories", "id_category"],
        data() {
            return {
                processing: false,
                customer: {
                    fullname: "",
                    email: "",
                    phone: "",
                    newsletter_subscription: true,
                    notifications: true,
                },
                nextStep: 4,
                errors: []
            }
        },
        mounted: function () {
          this.$refs.fullname.focus();
            // fill with default values
            // let date = Date.now();
            // let str = (""+date).substring(6, 10);
            // this.customer.fullname = "customer"+str;
            // this.customer.email = str+"@gmail.com";
            // this.customer.phone = ""+date;
        },
        methods: {
            genRandomStr(){
                return Math.random().toString(36).substr(2, 5);
            },
            completeThree() {
                // check if all data required is present
                this.checkData(); //
                if (this.errors.length == 0) {
                    this.storeCustomerAndAnswers();
                }
            },
            // check data and return errors if any
            checkData() {
                this.clearErrors();

                // validations
                this.errors = this.errors.concat(validateStrField(this.customer.fullname, "nombre", 2));
                this.errors = this.errors.concat(validateEmailField(this.customer.email));
                this.errors = this.errors.concat(validatePhoneField(this.customer.phone));
            },
            clearErrors() {
                this.errors = [];
            },
            storeCustomerAndAnswers() {
                this.processing = true;
                // add some more data to store
                this.customer.id_category = this.id_category;
                this.customer.filtered_questions = this.$attrs.filtered_questions;

                // console.log(JSON.stringify(this.customer.filtered_questions));
                axiosPostApi('/customer_and_answers', {"item": this.customer}, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
                        // show errors for the user to know them
                        this.errors = res.data.errors;
                    } else {
                        // all OK $emit the completion event and emit the id of the customer
                        if (res.data.hasOwnProperty("item_id")) {
                            console.log("reached emit! width item.id", res.data.item_id);
                            this.$emit(
                                "evcompletethree",
                                this.nextStep,
                                res.data.item_id,
                                res.data.plans
                            );
                        } else {
                            console.log("reached here!");
                            this.errors.push("No se pudieron guardar sus datos, intente de nuevo más tarde por favor");
                        }
                    }
                }, () => {
                    this.processing = false;
                });
            },
            storeCustomer() {
                this.processing = true;
                this.customer.id_category = this.id_category;
                axiosPostApi('/customer', {"item": this.customer}, res => {
                    if (res.data.hasOwnProperty("errors") && res.data.errors.length) {
                        // show errors for the user to know them
                        this.errors = res.data.errors;
                    } else {
                        // all OK $emit the completion event and emit the id of the customer
                        if (res.data.hasOwnProperty("item_id")) {
                            console.log("reached emit! width item.id", res.data.item_id);
                            this.$emit("evcompletethree", this.nextStep, res.data.item_id);
                        } else {
                            console.log("reached here!");
                            this.errors.push("No se pudieron guardar sus datos, intente de nuevo más tarde por favor");
                        }
                    }
                }, () => {
                    this.processing = false;
                });
            }
        },
    }
</script>

<style scoped>

</style>
