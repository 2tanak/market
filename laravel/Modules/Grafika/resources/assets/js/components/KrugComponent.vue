<template>
<div class='container-fluid' style='border:1px solid red'>
<div class='row'>

<canvas id="myChart" width="100" height="100"></canvas>

</div>
  </div>

</template>

<script>

import axios from 'axios';
import ChartDataLabels from 'chartjs-plugin-datalabels';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'
ChartJS.register(ArcElement, Tooltip, Legend,ChartDataLabels)
//import $ from 'jquery';
//import pieChart from '../pieChart.js';
//alert($('meta[name="csrf-token"]').attr('content'));
    export default {
	
    data:function () {
    return {
      data:null,
	  ctx:null,
	  options:{
		   tooltips: {
    enabled: false,
  },
		  responsive: true,
		  maintainAspectRation: false,
		  
		   plugins: {
		  ChartDataLabels:
		      {
			//color: 'black',
            //anchor: 'end',
            //align: 'end',
            //offset: 15,
			//font: {size:150,weight: 500},
		     formatter: function(value, context) {
          return context.chart.data.labels[context.dataIndex];
        }
			  },
		 
		  },
		  },
	 
	  //type: 'bar',
    }
  },
  components: {
	   Pie
	  //pieChart:pieChart
  },
      methods: {
		  
         sendMessage: function(e) {
		 
		 var ctx = document.getElementById("myChart");
alert(1)
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["2 Jan", "9 Jan", "16 Jan", "23 Jan", "30 Jan", "6 Feb", "13 Feb"],
        datasets: [{
            data: [6, 87, 56, 15, 88, 60, 12],
            backgroundColor: "#4082c4"
        }]
    },
    options: {
    		"hover": {
        	"animationDuration": 0
        },
        "animation": {
        	"duration": 1,
						"onComplete": function () {
							var chartInstance = this.chart,
								ctx = chartInstance.ctx;
							
							ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontSize, Chart.defaults.global.defaultFontStyle, Chart.defaults.global.defaultFontFamily);
							ctx.textAlign = 'center';
							ctx.textBaseline = 'bottom';

							this.data.datasets.forEach(function (dataset, i) {
								var meta = chartInstance.controller.getDatasetMeta(i);
								meta.data.forEach(function (bar, index) {
									var data = dataset.data[index];                            
									ctx.fillText(data, bar._model.x, bar._model.y - 5);
								});
							});
						}
        },
    		legend: {
        	"display": false
        },
        tooltips: {
        	"enabled": false
         },
        scales: {
            yAxes: [{
            		display: false,
            		gridLines: {
                	display : false
                },
                ticks: {
                		display: false,
                    beginAtZero:true
                }
            }],
            xAxes: [{
            		gridLines: {
                	display : false
                },
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
			
			
			
			
         }
		 
      },
	   mounted() {
		   

			this.sendMessage();
		 }
	  
	  
    }
</script>
