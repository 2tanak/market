<template>
<div class='container-fluid' style='border:1px solid red'>
<div class='row'>

<div style="max-width:400px">
 <Pie v-if="data" :data="data" :options="options" />
</div>
</div>
  </div>

</template>

<script>

import axios from 'axios';
import datalabels from 'chartjs-plugin-datalabels';
import { Chart as ChartJS, ArcElement, Tooltip, Legend,Title } from 'chart.js'
import { Pie } from 'vue-chartjs'
ChartJS.register(ArcElement, Tooltip, Legend,datalabels,Title)
//import $ from 'jquery';
//import pieChart from '../pieChart.js';
//alert($('meta[name="csrf-token"]').attr('content'));
    export default {
	
    data:function () {
    return {
      data:null,
	  options:{
		responsive: true,
		maintainAspectRation: false,
		plugins: {
		   datalabels:
		   {
			   
			   
			//rotation: 90,  
			color: 'black',
            //anchor: 'end',
            align: 'end',
            //offset: -100,
			font: {size:16},
            
		   labels: {
            title: {
				//color: 'green',
              font: {
				  //weight: 'bold'
              }
            },
           value: {
            //color: 'green'
           }
        },
         formatter: function(value, context) {
               return context.chart.data.labels[context.dataIndex]+':'+' '+value;
               },		
			   
			   
		   },
			 tooltip: {
				   enabled:true
			   },
			   /*
		      title: {
                display: true,
                text: 'Custom Chart Title',
                padding: {
                    top: 10,
                    bottom: 30
                }
            },
			*/
		    legend: {
              display: true 
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
		 
		   axios.get('charviewkrug')
			.then(response => {
				this.data = response.data;
            })
			
			
			
			
         }
		 
      },
	   mounted() {
		   

			this.sendMessage();
		 }
	  
	  
    }
</script>
