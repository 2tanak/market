<template>
    <div class="container">
	<div class="card-body">
	<div class="row mb-3">
	 <label for="email" class="col-md-4 col-form-label text-md-end">
	 Email Address
	 </label>
	 <div class="col-md-6"><input type="email" class="form-control"/></div>
	  <span class="invalid-feedback" role="alert">
         <strong></strong>
       </span>
	</div>
	
	  <div class="row mb-3">
         <label for="password" class="col-md-4 col-form-label text-md-end">
		  Password
		 </label>
       <div class="col-md-6">
          <input id="password" type="password" class="form-control" name="password" required autocomplete="current-password">

        <span class="invalid-feedback" role="alert">
           <strong></strong>
         </span>
        </div>
      </div>
	
	
	  <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" 
								class="btn btn-primary"
								@click="auth"
								>
                                    Login
                                </button>

                              
                            </div>
                        </div>
	
        
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
        auth: function() {
			const instance = axios.create({
                                baseURL: 'http://localhost',
                                withCredentials: true
                           });
			
			 instance.get('/api/csrf-cookie', {headers: {
                                            "X-Requested-With": "XMLHttpRequest",
                                             //Accept: "application/json",
		                                     //Referer:"localhost/login",
											 withCredentials: true
	                                        },
	                                       //withCredentials: true,
	        }).then(response =>{
				const token = response.data.accessToken;;
				console.log(token);
				
				
				
				
				const config = {
      headers: {
       //"Referer":"localhost:3007",
	   "Accept": "application/json",
	   //"X-XSRF-TOKEN": token,
      }
     };
const postData = {
  email: 'admin@mail.ru',
  password: 12345678
};
instance.post('/check', postData,config)
  .then(r => {
	  
	
    console.log('Response:', r);
  })
				
				
				
				
				
				
				
				
				
				
				
				
			});
			alert(24);return false;
		   
          
        }
    }
};
</script>
