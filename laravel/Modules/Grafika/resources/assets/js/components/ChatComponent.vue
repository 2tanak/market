<template>
    <div class="container">
        <form action="#" id="form" method="post">
            <div class="input-group mb-3">
                <textarea
                    rows="6"
                    style="width:500px"
                    class="form-controll"
                    v-model="messagetextarea"
                >
                </textarea>
            </div>
            {{ dataMessage.join("\n") }}
            <div class="input-group-append">
                <button @click="sendMessage" class="btn btn-outline-secondary">
                    отправить
                </button>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    data: function() {
        return {
            dataMessage: [],
            messagetextarea: "",
            inputValue: ""
        };
    },
    methods: {
        sendMessage: function(e) {
            e.preventDefault();
			
			//console.log(this.messagetextarea );return false;
            axios({
                method: "post", //you can set what request you want to be
                url: "chat-index",
                data: { messagetextarea: this.messagetextarea },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                }
            }).then(response => {
				console.log(response);
                console.log(response.data);
                this.dataMessage.push(response.data);
                this.messagetext = "";
            });
        }
    },

    mounted() {
        var socket = io("http://localhost:3000");
        socket.on(
            "laravel_database_action:chat",
            function(data) {
				
                console.log(data.message);
                this.dataMessage.push(data.message);
            }.bind(this)
        );

        //this.sendMessage();
    }
};
</script>