document.addEventListener("DOMContentLoaded", function (event) {
    initApp();
});

let appInstance = null;

function initApp() {
    appinstance = new Vue({
        el: document.getElementById("scrapper-app"),
        data() {
            return {
                message: "Test message",
            }
        },
        mounted: function () {
        },
        methods: {}
    });
}
