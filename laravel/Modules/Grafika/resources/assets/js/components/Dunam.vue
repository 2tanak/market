<template>
    <div class="container" style="border:1px solid red">
        <p>
            <button class="btn btn-primary" @click="sendMessage">
                отправить
            </button>
        </p>
        <div class="row">
            <line-chart
                :chart-data="data"
                :height="200"
                :options="{ responsive: true, maintainAspectRation: true }"
            >
            </line-chart>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import $ from "jquery";
import lineChart from "../lineChart.js";
//alert($('meta[name="csrf-token"]').attr('content'));
export default {
    data: function() {
        return {
            data: []
        };
    },
    components: {
        lineChart: lineChart
    },
    methods: {
        sendMessage: function(e) {
            axios.get("/char-view-dunam").then(response => {
                console.log(response.data);
                this.data = response.data;
            });
        }
    },
    mounted() {
        this.sendMessage();
    }
};
</script>
