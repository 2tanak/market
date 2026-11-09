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
		  <div class="col-md-4">
		   <ul>
		     <li v-for="user in activeUsers">
			 {{user.name}}
			 </li>
		   </ul>
		 </div>
		 
		 
		 
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
	  activeUsers:[]
    }
  },
  props:[
  'room2',
  'user'
  ],
  computed:{
	  channel(){
	  return window.Echo.join('room.'+this.room2.id);
	  }
  },
  methods: {
         sendMessage: function(e) {
			 
		 axios({
			   method: 'post', //you can set what request you want to be
               url: '/online-ajax',
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
			//console.log(this.room2);
		this.channel
		.here((users)=>{
			this.activeUsers=users;
		})
		.joining((user)=>{
			this.activeUsers.push(user);
		})
		.leaving((user)=>{
			this.activeUsers.splice(this.activeUsers.indexOf(user,1));
		})
		.listen('Ktoonline',({data})=>{
			this.message2.push(data.text);
			this.isActive = false;
        })
		.listenForWhisper('typing',(e)=>{
			this.isActive = e;
			if(this.typingTimer) {clearTimeout(this.typingTimer)}
			this.typingTimer = setTimeout(()=>{this.isActive = false;},2000)
		})
		   	  

			//this.sendMessage();
        }
		
    }
</script>