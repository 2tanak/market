<template>
    <div class="container" style="border:1px solid red">
        <div class="row">
		
			<Line v-if="data" :data="data" :options="options" />
        </div>
    </div>
</template>

<script>
import axios from "axios";
//import $ from "jquery";

import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import {data}  from '../chartConfig.js'


ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
)

export default {
    data: function() {
       return {
		   data:null,
		   options:{
			   responsive: true,
               maintainAspectRatio: false
		   },
	   }
    },
    components: {
		Line
        //lineChart: lineChart
    },
    methods: {
        sendMessage: function(e) {
            axios.get("char").then(response => {
                this.data = response.data
            });
        }
    },
    mounted() {
        this.sendMessage();
    }
};
</script>
