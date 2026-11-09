<template>
  <div class='container-fluid' style='border:1px solid red'>
    <div class='row'>
      <!-- Задаем фиксированную высоту контейнеру, чтобы круговая диаграмма не сжалась -->
      <div style="max-width:400px; height:400px; position: relative;">
        <!-- 🔥 Передаем плагин datalabels в проп :plugins -->
        <Pie v-if="data" :data="data" :options="options" :plugins="chartPlugins" />
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import datalabels from 'chartjs-plugin-datalabels';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, Title } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend, Title)

export default {
  name: 'KrugcharComponent',
  components: {
    Pie
  },
  data: function () {
    return {
      data: null,
      // 🔥 Передаем плагин в массив, чтобы привязать к тегу <Pie />
      chartPlugins: [datalabels], 
      options: {
        responsive: true,
        maintainAspectRatio: false, // 🔥 Исправлена опечатка (было maintainAspectRation)
        plugins: {
          datalabels: {
            color: 'black',
            align: 'end',
            font: { size: 16 },
            formatter: function(value, context) {
              return context.chart.data.labels[context.dataIndex] + ': ' + value;
            },
          },
          tooltip: {
            enabled: true
          },
          legend: {
            display: true 
          },
        },
      },
    }
  },
  methods: {
    sendMessage: function() {
      axios.get('charviewkrug')
        .then(response => {
          this.data = response.data;
        })
        .catch(error => {
          console.error("Ошибка загрузки кругового графика:", error);
        });
    }
  },
  mounted() {
    this.sendMessage();
  }
}
</script>
