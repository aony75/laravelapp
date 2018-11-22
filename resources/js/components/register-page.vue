<template>
    <div>

          <!--processing gif-->
          <processing-gif :processing="processing"
                          imgSrc="../../../images/processing.gif"
                          loaderText="Registering please wait...">
          </processing-gif>
            
          <!--danger alert-->
          <b-alert v-for="(error, index) in registerErrors" 
                   :key="index"  
                   show
                   variant="danger">
            {{error}}
          </b-alert>
            
          <!--card body-->
          <b-card border-variant="secondary"
                  header-bg-variant="light"
                  style="max-width: 600px; margin: auto;">
            <h1 slot="header" 
                class="text-center">
                REGISTER
            </h1>
      
            <!--user form-->
            <b-form>
                
              <!--name input-->
              <b-form-input id="name" 
                            class="mb-4"
                            v-model="registerForm.name" 
                            type="text" 
                            size="lg"
                            placeholder="Name" />
      
              <!--username input-->
              <b-form-input id="username" 
                            class="mb-4"
                            v-model="registerForm.email" 
                            type="text" 
                            size="lg"
                            placeholder="Username" />
                           
              <!--password input-->        
              <b-form-input id="password" 
                            v-model="registerForm.password" 
                            type="password" 
                            size="lg"
                            placeholder="Password" />
                
              <!--password confirmation input-->        
              <b-form-input id="password-confirmation" 
                            v-model="registerForm.password_confirmation" 
                            type="password" 
                            size="lg"
                            class="mt-4"
                            placeholder="Confirm Password" />
      
              <!--bav to center buttons-->              
              <b-row class="justify-content-center mt-4">
      
                <!--submit form button-->  
                <b-button type="submit"
                          class="mr-4"
                          variant="success" 
                          size="lg"
                          @click.prevent="handleRegisterClick">
                  Register
                </b-button>
      
                <!--back to login button-->  
                <b-button variant="primary"
                          class="text-white"
                          size="lg"
                          @click="handleLoginButtonClick">
                  Back to login
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
                registerForm: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
              },
      
              registerErrors: [],
      
              processing: false
            });
          },
      
          methods: {
            //registers user on register click
            handleRegisterClick(){
              this.registerUser();
            },
            
            //handles register request 
            registerUser(){
              let self = this;
      
              //sets processing gif
              self.processing = true;
      
              axios({
                method: 'post',
                url: 'register',
                headers: {
                  'Accept': 'application/json',
                  'Content-Type': 'application/json'
                },
                data: JSON.stringify(self.registerForm)
              })
              .then(function(res){
                //clears inputs and register errors after request has beeen made
                self.registerForm.password = '';
                self.registerForm.password_confirmation = '';
                self.registerErrors = [];
                
                //turns gif off
                self.processing = false;

                //creates error alerts for user if field validation fails
                if(res.data.errors){
                    for(let error in res.data.errors){
                        //issues replacing email for auth guard so this is part of my workaround
                        if(error == 'email'){
                            self.registerErrors.push(res.data.errors[error][0].replace(error, 'username'));
                        }else{
                            self.registerErrors.push(res.data.errors[error][0]);
                        }
                    }
                }else if(res.data.success){
                    //redirects to login with registration success to activate success alert 
                    window.location.href = 'login?register_success=true';
                }

              })
              .catch(function(err){
                //turns off gif and handles axios error
                console.log('axios get user error: '+err);
                self.processing = false;
                //error alert shows if server sends something unexpected
                self.registerErrors = ['Something went wrong with the server. Refresh and try again.'];
              });
            },
            
            //redirects to login page
            handleLoginButtonClick(){
              window.location.href = 'login';
            }
          }
        }
      </script>