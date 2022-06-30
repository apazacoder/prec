<template>
  <div class="main card">
    <div style="width: 100%">
      <canvas id="customersChart" width="1200" height="400"></canvas>
    </div>
  </div>
</template>

<script>
const Chart = require('chart.js');
export default {
  name: "Home",
  data() {
    return {
      names: ["", "", "", "", ""],
      freqs: [0, 0, 0, 0, 0],
      startAt: ''
    }
  },
  methods: {
    genGraph() {
      var options = {
        scales: {
          yAxes: [{
            // ticks: {
            //   beginAtZero: true
            // }
          }],
          xAxes: [{
            // ticks: {
            //   beginAtZero: true
            // }
          }],
        },
      };
      let ctx = document.getElementById("customersChart");

      let data1 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2.700];
      let data2 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 2.417];
      let data3 = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1.923];
      let labels = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '21/03/21'];
      // randomize

      for (let i = 0; i < 29; i++) {
        data1[i] = Math.random() * 3;
        data2[i] = Math.random() * 3;
        data3[i] = Math.random() * 3;
        labels[i] = (i+1)+"/03/21";
      }

      var mixedChart = new Chart(ctx, {
        data: {
          datasets: [{
            type: 'line',
            label: 'Oliva virgen extra',
            data: data1,
            borderColor: "#009900",
            backgroundColor: "rgba(0,0,0,0.05)",
            borderWidth: 1,
            lineTension: 0
          }, {
            type: 'line',
            label: 'Oliva virgen',
            data: data2,
            borderColor: "#820000",
            lineTension: 0,
            borderWidth: 1,
            backgroundColor: "rgba(0,0,0,0.05)",
          }, {
            type: 'line',
            label: 'Oliva lampante',
            data: data3,
            borderColor: "#0960B1",
            lineTension: 0,
            borderWidth: 1,
            backgroundColor: "rgba(0,0,0,0.05)",
          }],
          labels: labels
        },
        options: options
      });
    }
  },
  mounted() {
    // axiosGet(`${API_URL}/cashflows/maingraph`, (res) => {
    //   this.names = res.data.names;
    //   this.freqs = res.data.freqs;
    //   this.startAt = res.data.startAt;
    //   this.genGraph();
    // });
    // setTimeout(function(){
    this.genGraph();
    // }, 1000);
  }
}
</script>

<style scoped>

</style>
