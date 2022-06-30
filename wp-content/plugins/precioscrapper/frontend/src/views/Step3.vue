<template>
    <div class="card step-card" v-if="step == currentStep">
        Step 3
        <router-link to="/step2">Paso 2</router-link>
        <router-link to="/step4">Paso 4</router-link>
    </div>
</template>

<script>
    export default {
        name: "Step3"
    }
</script>

<style scoped>

</style>
