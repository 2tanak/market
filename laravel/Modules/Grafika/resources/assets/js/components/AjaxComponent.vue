<template>
    <div class="container">
        <button
            @click="update"
            type="button"
            class="btn btn-primary"
            v-if="!is_refresh"
        >
            обновить {{ id }}
        </button>
        <span v-if="is_refresh" class="badge badge-primary">
            ОБНОВИТЬ ......</span
        >

        <div class="table">
            <thead>
                <tr>
                    <td>намменвание</td>
                    <td>ссылка</td>
                </tr>
            </thead>
            <tbody>
                <tr v-for="value in url">
                    <td>{{ value.title }}</td>
                    <td>{{ value.url }}</td>
                </tr>
            </tbody>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data: function() {
        return {
            url: [],
            if_refresh: false,
            id: 0
        };
    },
    mounted() {
        this.update();
        console.log("Component mounted.");
    },
    methods: {
        update: function() {
			const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
		   
            axios({
                method: "get", //you can set what request you want to be
                url: "ajax2",
                params: {
                    filter: "filter"
                },
                headers: {
                    "X-CSRF-TOKEN": token
                }
            }).then(response => {
				
                this.url = response.data;
                console.log(response.data);
            });
        }
    }
};
</script>
