<template>
  <div>
    <!--processing gif-->
    <processing-gif :processing="processing"
                    imgSrc="../../../images/processing.gif"
                    :loaderText="loaderText">
    </processing-gif>
      
    <!--danger alert-->
    <b-alert v-if="loginError != null" 
             show
             variant="danger">
      {{loginError}}
    </b-alert>

    <!--success alert-->
    <b-alert v-if="registerSuccess == true" 
             show
             variant="success">
      Registration successful.
    </b-alert>
      
    <!--card body-->
    <b-card border-variant="secondary"
            header-bg-variant="light"
            style="max-width: 600px; margin: auto;">
      <h1 slot="header"
          class="text-center">
          LOGIN
      </h1>

      <!--user form-->
      <b-form>

        <!--username input-->
        <b-form-input id="username" 
                      class="mb-4"
                      v-model="loginForm.email" 
                      type="text" 
                      size="lg"
                      placeholder="Username" />
        
        <!--password input-->        
        <b-form-input id="password" 
                      v-model="loginForm.password" 
                      type="password" 
                      size="lg"
                      placeholder="Password" />

        <!--bav to center buttons-->              
        <b-row class="justify-content-center mt-4">

          <!--submit form button-->  
          <b-button type="submit"
                    class="mr-4"
                    variant="success" 
                    size="lg"
                    @click.prevent="handleSignInClick">
            Sign in
          </b-button>

          <!--create username button-->  
          <b-button variant="primary"
                    class="text-white"
                    size="lg"
                    @click="handleCreateUsernameClick">
            Create Username
          </b-button>
        </b-row>
      </b-form>
    </b-card>
  </div>
</template>

<script>
  const axios = require('axios');

  export default {

    data() {
      return ({
        //using email as username as a workaround for issue with auth guard 
        loginForm: {
          email: '',
          password: '',
        },
        
        registerSuccess: false,

        loginError: null,

        processing: false, 

        loaderText: 'assigning credentials please wait...'
      });
    },

    created(){
      this.handleGetRequest();
    },

    methods: {
      //checks if get request is a redirect from registration page and clears access token
      handleGetRequest(){
        localStorage.clear();

        /*Alert shows for browsers that don't support ES6. 
        Without ES6 promises will not work, so axios and urlParams will not work.*/
        try{
          let urlParams = new URLSearchParams(window.location.search);

          if(urlParams.has('register_success')){
            this.registerSuccess = true;
          }
        }catch(err){
          this.loginError = 'ALERT: Your browser is not up to date. '+
          'Please update the current browser or try another one. '+
          'SUGGESTION: Try Google Chrome.';
        }
      },

      //login if username and password are correct. Returns error if credentials are incorrect.
      handleSignInClick(){
        this.getAccessToken();
      },
      
      //stores access token in local storage
      getAccessToken(){
        let self = this;
        self.registerSuccess = false;

        //sets processing gif
        self.processing = true;

        axios({
          method: 'post',
          url: 'login',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          data: JSON.stringify(self.loginForm)
        })
        .then(function(res){
          //clears inputs and login errors after request has beeen made
          self.loginForm.email = '';
          self.loginForm.password = '';
          self.loginError = null;
          
          //if access token isn't present error handling occurs
          if(res.data.access_token == undefined){ 
            self.loginError = res.data.error;

            //truns of processing gif and resets loader text
            self.processing = false;
            self.loaderText = 'assigning credentials please wait';
          }else{

            //checks that local storage can be used 
            if (typeof(Storage) !== "undefined"){
              //changes processing gif text, stores access token, then goes to home page
              self.loaderText = 'logging in...'; 
              localStorage.setItem('access_token', res.data.access_token);
              window.location.href = 'home';
            }else{
              //turns of gif, resets loader text, and loads handles local storage error 
              self.loginError = 'Looks like your browser does not support local storage Please try another browser or update this one.';
              self.processing = false;
              self.loaderText = 'assigning credentials please wait';
            }
          }
        })
        .catch(function(err){
          //turns off gif, resets loader text, and handles axios error
          console.log('axios get user error: '+err);
          self.processing = false;
          self.loginError = 'Something went wrong with the server. Refresh and try again.';
          self.loaderText = 'assigning credentials please wait';
        });
      },
      
      //redirects to register page
      handleCreateUsernameClick(){
        window.location.href = 'register';
      }
    }
  }
</script>