<template>
    <div class="progress" :class="isLoading ? '' : 'inactive'">
        <div :class="isLoading ? 'indeterminate': ''"></div>
    </div>
</template>

<script>
    export default {
        name: "ProgressBar",
        props: ["isLoading"],

    }
</script>

<style scoped>
    .progress.inactive {
        background: transparent;
    }
</style>
