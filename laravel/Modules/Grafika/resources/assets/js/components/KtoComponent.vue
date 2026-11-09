<template>
    <div class="container">
        
     
	</br>
         <div class="col-md-12">
		 <input type ="text" class="form-control" placeholder="наберите сообщения" v-model="messagetext" @keydown="actionUser">
		 <span v-if="isActive">{{isActive.name}} набирает текст</span>
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
	  isActive: false,
	  typingTimer:false,
    }
  },
  props:[
  'room2',
  'user'
  ],
  computed:{
	  channel(){
	  return window.Echo.private('room.'+this.room2.id);
	  }
  },
  methods: {
         sendMessage: function(e) {
			 
		 axios({
			   method: 'post', //you can set what request you want to be
               url: '/echo-event-ajax',
			   		   data: {text:this.messagetext,room_id:this.room2.id},

           headers:{
			   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            })
			.then(response => {
				console.log(response.data);
			  //this.message2.push(response.data);
				this.messagetext="";
						

            })
			},
		 actionUser(){
			 this.channel.whisper('typing',{name:this.user.name})
		 }
      },
	  
        mounted() {
				  

			console.log(this.room2);
		this.channel.listen('Echoevent',({data})=>{
			this.message2.push(data.text);
			this.isActive = false;


		}).listenForWhisper('typing',(e)=>{
			this.isActive = e;
			if(this.typingTimer) {clearTimeout(this.typingTimer)}
			this.typingTimer = setTimeout(()=>{this.isActive = false;},2000)
		})
		   	  

			//this.sendMessage();
        }
		
    }
</script>