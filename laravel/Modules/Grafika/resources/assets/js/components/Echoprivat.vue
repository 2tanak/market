<template>
    <div class="container">
        
     
	</br>
         <div class="col-md-12">
		 <input type ="text" class="form-control" placeholder="наберите сообщения" v-model="messagetext">
		 </div>
		 </br>
		 <div class="col-md-12">

		 <textarea rows=6 style="width:500px" class="form-controll">
		 
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
  props:[
  'room2',
  ],
  methods: {
         sendMessage: function(e) {
		
		 axios({
			   method: 'post', //you can set what request you want to be
               url: '/echo-privat-ajax',
			   		   data: {text:this.messagetext,room_id:this.room2.id},

           headers:{
			   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            })
			.then(response => {
				
			console.log(response.data);
			  //this.message2.push(response.data);
				this.messagetext="";
				//this.message2.push(response.data.text);

            })
			},
		 
      },
	  
        mounted() {
			//console.log(this.room2);
		window.Echo.private('room.'+this.room2.id).listen('Echoserverprivat',({data})=>{
		
			console.log(5555);
						  this.message2.push(data.text);

		});
		   	  

			//this.sendMessage();
        }
		
    }
</script>