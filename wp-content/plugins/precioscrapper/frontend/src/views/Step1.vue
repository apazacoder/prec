<template>
    <div class="card step-card">
        <div class="question">
            ¿En qué categoría te encuentras?
        </div>
        <label v-for="category in categories">
            <label>
                <input type="radio" name="radio">
                <span v-text="category.name"></span>
            </label>
        </label>



    </div>
</template>

<script>
    export default {
        name: "Step1",
        props: ["categories"],
        data(){
            return {
                processing: false,
            }
        },
        mounted: function(){

        },
        methods:{
        }
    }
</script>

<style scoped>

</style>
