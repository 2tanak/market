<template>
    <div class="container">
        
     
	</br>
         <div class="col-md-12">
		 <input type ="text" class="form-control" placeholder="наберите сообщения" v-model="messagetext">
		 </div>
		 </br>
		 <div class="col-md-12">

		 <textarea rows=6 style="width:500px" class="form-controll" v-model = "message2" >
		 
		 {{message2.join('\n')}}
		 </textarea>
</div>

 
         
 <div class="input-group-append">
   
 <button @click="sendMessage" class='btn btn-outline-secondary'>отправить</button>
 
 </div>

 
 
 </div>

 

     
    
</template>

<script>
    export default {
		   data:function () {
    return {
	  message2:[],
      messagetext:"",
	  inputValue:'',
    }
  },
  methods: {
         sendMessage: function(e) {
			 
		 axios({
			   method: 'post', //you can set what request you want to be
               url: '/echo-chat-public-ajax',
			   		   data: {text:this.messagetext},

           headers:{
			   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            })
			.then(response => {
				console.log(response.data);
			    this.message2.push(response.data.text);
				this.messagetext="";
						

            })
			},
		 
      },
	  
        mounted() {
			//alert(window.location.hostname + ":6001");
		window.Echo.channel('chat').listen('Echoserverpublic',({message})=>{
			
						  this.message2.push(message);

		});
		   	  

			//this.sendMessage();
        }
		
    }
</script>