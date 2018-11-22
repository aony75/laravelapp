<template>
    <div>
        <!--processing gif-->
        <processing-gif :processing="processing"
                        imgSrc="../../../images/processing.gif"
                        loaderText="please wait...">
        </processing-gif>

        <!--danger alert-->
        <b-alert v-for="(error, index) in errors"show 
                 :key="index" 
                 variant="danger">
            {{error}}
        </b-alert>

        <!--staff table-->
       <staff-table id="staff-table" 
                    :entryData="people" 
                    :key="person.UUID" 
                    :tableFields="tableFields"
                    :pagination="pagination" 
                    :entryModalId="entryModalId"
                    @editButtonClick="handleEditButtonClick($event)" 
                    @newButtonClick="handleNewButtonClick"
                    @deleteEntry="confirmEvent(handleDeleteEntry, $event)" 
                    @filtersChange="handleFiltersChange($event)" 
                    @paginationClick="handlePaginationClick($event)" 
                    @updateEntryData="handleUpdateEntryData($event)">
        </staff-table>

        <!--modal for staff entry-->
        <app-modal :id="entryModalId"
                    :modalTitle="modalTitle"
                    :modalErrors="modalErrors" 
                    :saveInProgress="saveInProgress" 
                    :showValue="showEntryModal" 
                    @hidden="handleEntryModalHidden"
                    @shown="handleEntryModalShown">
            
            <!--app modal body-->
            <div slot="body">
                
                <!--app modal success alert for new staff-->
                <b-alert v-if="modalSuccess === true && !person.id" show
                         variant="success">
                    New entry successfully created
                </b-alert>

                <!--app modal success alert for updating staff-->
                <b-alert v-if="modalSuccess === true && person.id" show
                         variant="success">
                    Entry successfully updated
                </b-alert>
                <b-row class="mb-2">

                    <b-col>
                        
                        <!--UUID-->
                        <p v-if="person.id"
                           class="text-left mt-1">
                            UUID: {{person.id}}
                        </p>
                    </b-col>
                    <b-col>
                        
                        <!--edit checkbox-->
                        <b-checkbox v-if="person.id"
                                    id="edit-button"
                                    class="float-right mt-1" 
                                    v-b-tooltip.hover 
                                    :title="editButtonTitle" 
                                    v-model="formDisabled"
                                    value="false" 
                                    unchecked-value="true">
                        </b-checkbox>
                        
                        <!--edit checkbox label-->
                        <p v-if="person.id" 
                           class="float-right mr-2 mt-1">
                           EDIT FORM:
                        </p>
                    </b-col>
                </b-row>
                
                <!-- entry form-->
                <b-form>

                    <!--first name label and input-->
                    <b-form-group 
                        id="first_name_group" 
                        label="First Name:" 
                        label-for="first_name">
                            <b-form-input id="first_name" 
                                          v-model="person.firstName" 
                                          type="text" 
                                          placeholder="First Name"
                                          :disabled="isFormDisabled" />
                    </b-form-group>

                    <!--last name label and input-->
                    <b-form-group id="last_name_group"
                                  label="Last Name:" 
                                  label-for="last_name">
                        <b-form-input id="last_name" 
                                      v-model="person.lastName" 
                                      type="text" 
                                      placeholder="Last Name" 
                                      :disabled="isFormDisabled" />
                    </b-form-group>

                     <!--code label and input-->
                    <b-form-group id="code_group"
                                  label="Code:" 
                                  label-for="code">
                        <b-form-input id="code" 
                                      v-model="person.code" 
                                      type="text" 
                                      placeholder="Code" 
                                      :disabled="isFormDisabled" />
                    </b-form-group>

                     <!--status label and input-->
                    <b-form-group id="status_group"
                                  label="Status:"
                                  label-for="status">
                        <b-form-select id="status"
                                       :options="statusOptions" 
                                       v-model="statusSelected"
                                       :disabled="isFormDisabled">
                        </b-form-select>
                    </b-form-group>

                    <!--description label and input-->
                    <b-form-group id="description_group"
                                  label="Description:"
                                  label-for="description">
                        <b-form-textarea id="description" 
                                         v-model="person.description"
                                         placeholder="Enter description here"
                                         :rows="4"
                                         :disabled="isFormDisabled">
                        </b-form-textarea>
                    </b-form-group>

                    <!--placeholder for buttons-->
                    <p v-if="isFormDisabled == true" 
                    class="mt-2 text-success text-center font-weight-bold">
                        Click edit checkbox above for save button
                    </p>

                    <!--save and cancel buttons-->
                    <b-row v-else 
                           class="justify-content-center mt-4">
                        <b-button type="submit"
                                  class="btn btn-success mt-2 mr-4" 
                                  @click.prevent="handleEntryModalSaveClick">
                            Save
                        </b-button>
                        <b-button class="btn btn-danger mt-2"
                                  @click="handleEntryModalCloseClick">
                            Cancel
                        </b-button>
                    </b-row>
                </b-form>
            </div>
        </app-modal>

         <!--confirm modal-->
        <app-modal id="confirmModal"
                   :showValue="showConfirmModal" 
                   size="md" 
                   modalTitle="confirm action"
                   :hideHeaderClose="true">

             <!--modal body-->
            <div slot="body">
                <p id="confirm-message" 
                   class="text-center">
                {{confirmMessage}}
            </p>

                <!--confirm modal ok and cancel buttons-->
                <b-row class="justify-content-center">
                    <b-button variant="primary"
                              class="mr-4"
                              @click="handleConfirmOk">
                        Ok
                    </b-button>
                    <b-button variant="secondary"
                              @click="handleConfirmCancelClick">
                        Cancel
                    </b-button>
                </b-row>
            </div>
        </app-modal>
    </div>
</template>

<script>
    const axios = require('axios');

    export default {
        data(){
            return ({
                /***********************************************************/
                //STAFF CONTENT DATA
                /************************************************************/ 
                errors: [],

                baseURL: 'api/people',

                currentURL: '',
                
                /***********************************************************/
                //STAFF TABLE DATA
                /************************************************************/ 
                pagination: {},

                people: [],

                tableFields: {
                    checkbox: {
                        label: ''
                    },
                    UUID: {
                        sortable: true
                    },
                    lastName: {
                        label:'Name',
                        sortable: true
                    },
                    code: {
                        label: 'Code',
                        sortable: true
                    },
                    status: {
                        label: 'Status',
                        sortable: true,
                    }, 
                    edit: {
                        label: 'Edit',
                        sortable: false
                    }, 
                    delete: {
                        label: 'Delete',
                        sortable: false
                    }
                },

                processing: false,

                /***********************************************************/
                //MODAL DATA
                /************************************************************/
                entryModalId: 'entryModalId',

                person: {
                    id: '',
                    firstName: '', 
                    lastName: '', 
                    description: '', 
                    code: '', 
                    status: '',
                    people_id: '',
                    current_signed_in_user: '',
                },

                saveInProgress: false,

                modalErrors: [],

                entryModalId: 'entryModal',

                modalSuccess: '',
                
                statusSelected: '',

                modalTitle: '',
                
                editEntryModal: false,

                formDisabled: '',

                statusOptions: [
                    {value: '', text: '-- Please select an option --', disabled: true},
                    {value: 'active', text:'active'},
                    {value: 'inactive', text:'inactive'}
                ],

                showEntryModal: false,

                showConfirmModal: false,

                confirmFunction: {},

                confirmMessage: ''
            });
        },

        computed: {
            //determines whether entry modal form is disabled or not 
            isFormDisabled: function(){
                return (this.formDisabled == true || this.formDisabled == 'true') ? true : false;
            },
            
            //sets edit checkbox label in edit entry mdoal 
            editButtonTitle: function(){
                const disabledTitle = "click to edit form";
                const notDisabledTitle = "click to lock form";

                return (this.formDisabled == true || this.formDisabled == 'true') ? disabledTitle :
                notDisabledTitle;
            }
        },

        created(){
            this.axiosGet();
        },

        mounted(){
            this.setRemoveAccessToken();
        },

        updated(){
            this.isEditOrNewForm();
        },

        methods: {
            /*********************************************************************/
            //STAFF CONTENT METHODS
            /**********************************************************************/
            //sets eventlistener on logout to clear access token on click 
            setRemoveAccessToken(){
                const logoutLink = document.getElementById('logout-link');

                logoutLink.addEventListener('click', function(){
                    localStorage.clear();
                });
            },

            //checks for access token everytime a request is made
            getAccessToken(){
                let accessToken = localStorage.getItem('access_token');

                if(accessToken != undefined){
                    return accessToken;
                }else{
                    this.errors = ['Error accessing api. '+
                    'Refresh and try again. If the problem persists, logout and try again.'];
                }
            }, 

            //checks for current user id everytime table requests are made
            async validateCurrentUserId(){
                let currentUserId = await this.getCurrentUserId();

                if(currentUserId != undefined && currentUserId != ''){
                    return currentUserId;
                }else{
                    this.errors = this.currentUserIdError();
                    return undefined;
                }
            },

            //error message if request is submitted and the current user id is not set
            currentUserIdError(){
                return ['Something went wrong with accessing your credentials. '+
                    'Refresh and try again. If the problem persists, logout and try again.'];
            },

            //get current user id
            async getCurrentUserId(){
                let self = this;
                let accessToken = self.getAccessToken();
                let currentUserId;

                await axios({
                    method: 'get',
                    url:'api/user',
                    headers: {
                        'Accept': 'application/json', 
                        'Authorization': 'Bearer '+accessToken
                    },
                })
                .then(function(res){
                    //loads current signed in user
                    currentUserId = res.data.id;
                    self.person.current_signed_in_user = res.data.id;
                })
                .catch(function(err){
                    //creates error alert for user if there is an error with axios
                    self.createErrorAlert();
                    console.log('axios get user error: '+err);
                });

                return currentUserId;
            },

            //get request
            async axiosGet(page_url){
                let self = this;
                let accessToken = self.getAccessToken();
                let current_user_id = await self.validateCurrentUserId();
                
                //clearing people acts as trigger for table gif. gif disappears when data is loaded
                self.people = [];
                
                //creates url with neccessary variables
                page_url = (page_url != undefined && page_url != '') ? page_url+'&current_signed_in_user='+current_user_id : 
                self.baseURL+'?current_signed_in_user='+current_user_id;

                await axios({
                    method: 'get',
                    url: page_url,
                    headers: {
                        'Accept': 'application/json', 
                        'Authorization': 'Bearer '+accessToken
                    },
                })
                .then(function(res){
                    
                    /*if data is empty and  meta total higher than 0, then there is a pagination 
                    click error from deleting rows. Table needs to refresh and remake pagination.
                    */
                    if((res.data.data.length == 0) && (parseInt(res.data.meta.total) > 0)){
                        self.axiosGet();
                    }else{
                        //loads retrieved entries
                        self.people = res.data.data;

                        //creates pagination 
                        self.makePagination(res.data.meta, res.data.links); 
                    }
                })
                .catch(function(err){
                    //creates error alert for user if there is an error with axios
                    self.createErrorAlert();
                    console.log('axios get error: '+err);
                });
            },

            //creates pagination object to hold pagination info
            makePagination(meta, links){
                //creates pagination with data from get response
                let pagination = {
                    current_page: meta.current_page, 
                    last_page:  meta.last_page,
                    next_page_url: links.next, 
                    prev_page_url: links.prev, 
                    first_page_url: links.first, 
                    last_page_url: links.last, 
                    total: meta.total,
                    from: meta.from,
                    to: meta.to,
                    path: meta.path,
                    per_page: meta.per_page,
                    spread: '2',
                    startingEllipsis: '',
                    endingEllipsis: '',
                    links: []
                };

                let arrayCount = 0;
                let startingPoint = parseInt(pagination.current_page) - parseInt(pagination.spread);
                let endingPoint = parseInt(pagination.current_page) + parseInt(pagination.spread);

                //decides starting and ending links for pagination
                startingPoint = (startingPoint > 0) ? startingPoint : 1;
                endingPoint = (endingPoint > pagination.last_page) ? pagination.last_page : endingPoint;

                //decides if there will be ellipsis for pagination
                pagination.startingEllipsis = startingPoint > 1;
                pagination.endingEllipsis = endingPoint < pagination.last_page;

                //sets ellipsis and first page link
                if(pagination.startingEllipsis){
                    pagination.links[arrayCount] = {
                        isActiveLink: false, 
                        href: pagination.first_page_url,
                        number: 1
                    };

                    arrayCount++;

                    pagination.links[arrayCount] = {
                        isActiveLink: false, 
                        href: '',
                        number: '...'  
                    };

                    arrayCount++;
                }

                //creates pagination page navigation links 
                for(let i = startingPoint; i <= endingPoint; i++){
                    pagination.links[arrayCount] = {
                        isActiveLink: i === parseInt(pagination.current_page), 
                        href: pagination.path+'?page='+i,
                        number: i  
                    };

                    arrayCount++;
                }

                //sets ellipsis and last page link
                if(pagination.endingEllipsis){
                    pagination.links[arrayCount] = {
                        isActiveLink: false, 
                        href: '',
                        number: '...'  
                    };

                    arrayCount++;

                    pagination.links[arrayCount] = {
                        isActiveLink: false, 
                        href: pagination.last_page_url,
                        number: pagination.last_page
                    };

                    arrayCount++;
                }

                this.pagination = pagination;
            },
            
            //staff section alert
            createErrorAlert(){
                this.errors = ['Something went wrong with the server. Refresh and try again.'];
            },
            
            //creates confirm modal and stores function that needs to be used
            confirmEvent(func, info){
                if(info.idSet.length != 0){
                    this.confirmMessage = info.confirmMessage; 
                    this.showConfirmModal = true;
                    this.confirmFunction = {
                        info: info,
                        func: ()=>func(info)
                    };
                }
            },

             /*********************************************************************/
            //STAFF TABLE METHODS
            /**********************************************************************/
            //keeps rows that were not mass deleted in the table
            handleUpdateEntryData(info){
                this.people = info;
            },

            /*when pagination click is emitted from the staff table component a request is made
            with the emitted url value*/
            handlePaginationClick(url){
                this.currentURL = url;
                this.axiosGet(url);
            },
            
            //request is made based on filter change
            handleFiltersChange(url){
                this.currentURL = url;
                this.axiosGet(url);
            },
            
            //converts string to array
            convertToArray(item){
               return (item.search(',') != -1) ? item.split(',') : new Array(item);
            },

            //handles delete entry event 
            async handleDeleteEntry(info){
                let emptyTable = ((this.people.length - info.idSet.length) == 0) ? true : false;
                
                //delete request only happens if current signed in user has a value
                if(this.validateCurrentUserId() != undefined){
                    //deletes entry or entries 
                    await this.deletePerson(info);
                }
                
                //refreshes table if table is empty after delete
                if(emptyTable === true){
                    await this.axiosGet();
                }
            }, 

            //deletes entry or entries
            async deletePerson(info){
                let self = this;
                let accessToken = self.getAccessToken();
                
                info.idSet = Array.isArray(info.idSet) ? info.idSet : 
                self.convertToArray(String(info.idSet));
                
                /*automatically exits out of function if mass delete is clicked and no entries
                were selected*/
                if(info.idSet.length == 0){
                    return;
                }

                //enables gif
                self.processing = (info.idSet.length == 0) ? false : true;
                    
                //sends delete request
                await axios({
                    method: 'delete',
                    url: self.pagination.path+'/'+info.idSet,
                    headers: {
                        'Accept': 'application/json', 
                        'Authorization': 'Bearer '+accessToken
                    },
                })
                .then(res=>{
                    //disables gif
                    self.processing = false;
                        
                    if(res.data.success == true){
                        /*transitions rows that are deleted with this work around
                        since transition group tag was not working with table*/
                        info.transitionDeletedRows();
                            
                        //modifies pagination based on entries deleted
                        self.pagination.to = parseInt(self.pagination.to) - info.idSet.length;
                        self.pagination.total = parseInt(self.pagination.total) - info.idSet.length;
                    }else{
                        //creates error if delete didn't return true
                        self.errors = ['Something went wrong with the delete request. Refresh and try again.'];
                    }
                })
                .catch(err =>{
                    //disables gif on error
                    self.processing = false;
                    //creates error alert for user if there is an error with axios
                    self.createErrorAlert();
                    console.log('axios delete error: '+err);
                });
            },

            //clears data from modal so old info doesn't show when it's reloaded
            clearPerson(){
                this.person.id = ''; 
                this.person.firstName = ''; 
                this.person.lastName = '';
                this.person.description = '';
                this.person.code = ''; 
                this.statusSelected = '';
            },

            //loads entry modal with entry data based on row to edit 
            handleEditButtonClick(person){
                this.editEntryModal = true;
                this.showEntryModal = true;
                this.person.id = person.UUID;
                this.person.people_id = person.UUID;
                this.person.firstName = person.firstName;
                this.person.lastName = person.lastName;
                this.person.description = person.description;
                this.person.code = person.code;
                this.statusSelected = person.status;
                this.modalTitle = 'Edit Entry';
            },

            //sets entry modal as new entry 
             handleNewButtonClick(){
                this.editEntryModal = false;
                this.showEntryModal = true;
                this.modalTitle = 'New Entry';
                this.clearPerson();      
            },

            /*********************************************************************/
            //MODAL METHODS
            /**********************************************************************/
            //excutes event if user clicks ok
            handleConfirmOk(){
                this.showConfirmModal = false;this.showConfirmModal = false;
                this.confirmFunction.func(this.confirmFunction.info);
                this.confirmFunction = {};
                this.confirmMessage = '';
            },
            
            //does not execute event if user cancels 
            handleConfirmCancelClick(){
                this.showConfirmModal = false;
                this.confirmFunction = {};
                this.confirmMessage = '';
            },

            //clears disabled and status values from modal 
            handleEntryModalShown(){
                this.formDisabled = '';
                this.selectedStatus = '';
                this.clearErrorsAndSuccess(); 
            },

            //closes modal on hidden event 
            handleEntryModalHidden(){
                this.showEntryModal = false;
            }, 

            //closes modal on close click event 
            handleEntryModalCloseClick(){
                this.showEntryModal = false;
            },
            
            //submits new entry
            handleEntryModalSaveClick(){
                this.clearErrorsAndSuccess();
                this.addOrEditPerson();
            },

            //determines whether entry modal fields should be disabled or not
            isEditOrNewForm(){
                if(this.formDisabled == ''){
                    this.formDisabled = this.editEntryModal ? true : false;
                }
            },
            
            //clears modal alerts
            clearErrorsAndSuccess(){
                this.modalErrors = [];
                this.modalSuccess = '';
            },

            //submits post or put based on the value of edit property
            addOrEditPerson(){
                let self = this;
                let accessToken = self.getAccessToken();
                
                //shows gif
                self.saveInProgress = true;

                /*determines method type for axios based on whether user is creating new 
                entry or editing an existing entry*/
                let methodType = self.editEntryModal === false ? 'post' : 'put';
                self.person.status = self.statusSelected;
                
                //request only happens if current signed in user has a value
                if(this.validateCurrentUserId() != undefined){
                    axios({
                        method: methodType,
                        url: self.pagination.path,
                        headers: {
                            'Accept': 'application/json', 
                            'Authorization': 'Bearer '+accessToken,
                            'Content-Type': 'application/json'
                        },
                        data: JSON.stringify(self.person)
                    })
                    .then(function(res){
                        //removes gif
                        self.saveInProgress = false;
                        
                        //handles validation errors from controller
                        if(res.data.errors){
                            let errorArray = [];

                            for(let error in res.data.errors){
                                errorArray.push(res.data.errors[error][0]);
                            }

                            self.modalErrors = errorArray;
                            document.body.scrollTop = 0;
                            document.getElementById('entryModal').scrollTop = 0;
                        }else{
                            document.body.scrollTop = 0;
                            document.getElementById('entryModal').scrollTop = 0;
                            self.modalSuccess = true;
                            self.axiosGet(self.currentURL);
                        }  
                    })
                    .catch(function(err){
                        //creates error for modal 
                        self.modalErrors =['Something went wrong with the server. Refresh and try again.'];

                        //creates error alert for user if there is an error with axios
                        self.createErrorAlert();
                        console.log('axios '+methodType+' error: '+err);
                    });
                }
            }
        }
    }
</script>