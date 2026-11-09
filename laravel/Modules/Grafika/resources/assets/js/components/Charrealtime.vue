<template>
    <div class="container-fluid" style="border:1px solid red">
        <div class="row">
            <line-chart
                :chart-data="data"
                :height="200"
                :width="800"
                :options="{ responsive: true, maintainAspectRation: true }"
            >
            </line-chart>
        </div>
        <input type="checkbox" v-model="realtime" />
        <input type="text" v-model="label" />
        <input type="text" v-model="sale" />
        <p>
            <button class="btn btn-primary" @click="senddata">отправить</button>
        </p>
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
            data: [],
            realtime: false,
            label: "",
            sale: 500
        };
    },
    components: {
        lineChart: lineChart
    },
    methods: {
        sendMessage: function(e) {
            axios.get("/grafik-realtime-ajax").then(response => {
                this.data = response.data;
            });
        },
        senddata: function() {
            axios({
                method: "get", //you can set what request you want to be
                url: "/grafik-realtime-ajax",
                params: {
                    label: this.label,
                    sale: this.sale,
                    realtime: this.realtime
                },

                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            }).then(response => {
                console.log(response.data);
                this.data = response.data;
            });

            return this.data;
        }
    },
    mounted() {
        var socket = io("http://localhost:3000");
        socket.on(
            "news-action:pospos",
            function(data) {
                console.log(data.result);
                this.data = data.result;
            }.bind(this)
        );

        this.sendMessage();
    }
};
</script>
