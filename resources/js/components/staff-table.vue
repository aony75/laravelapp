<template>
    <div>
        <div class="table-border mb-2">
            <b-row class="mb-2" 
                   :no-gutters="true">
                <b-col>

                    <!--per page select-->
                    <b-form-select id="per-page-select" 
                                   v-b-tooltip.hover 
                                   title="select entries per page" 
                                   v-model="perPageSelected" 
                                   variant="float-left" 
                                   :options="perPageOptions" 
                                   class="mr-2"
                                   size="sm" 
                                   @input="handleFilterInput">
                        <option :value="this.pagination.total">
                            all entries
                        </option>
                    </b-form-select>

                    <!--status filter select-->
                    <b-form-select id="status-filter" 
                                   variant="float-left" 
                                   v-model="statusFilterSelected"
                                   class="mr-2" 
                                   :options="statusFilterOptions"
                                   size="sm"
                                   v-b-tooltip.hover 
                                   title="filter by status"
                                   @input="handleFilterInput">  
                    </b-form-select>

                    <!--search input-->
                    <b-input id="search" 
                             v-b-tooltip.hover 
                             title="type to filter table" 
                             class="float-left mt-1 mr-2"
                             placeholder="Search by any field..." 
                             v-model="search" />
                </b-col>
                <b-col>

                     <!--new enty button-->
                    <b-button v-b-modal="entryModalId" 
                              variant="success float-right mt-3"
                              v-b-tooltip.hover 
                              title="add new entry"
                              @click="onNewButtonClick" >  
                        <i class="fas fa-plus">
                        </i>
                    </b-button>
                    
                    <!--mass delete button-->
                    <b-button variant="danger float-right mr-3 mt-3" 
                              v-b-tooltip.hover 
                              title="delete all selected entries"
                              @click="onDelete(selectedForDelete, entryDataOrder, 'mass delete')">
                        <i class="fas fa-trash-alt">
                        </i>
                    </b-button>

                    <!--current sort text-->
                    <p class="mt-4 mr-4 float-right">
                        Sorting by 
                        <span class=" font-weight-bold text-primary">
                            {{getSortBy}}
                        </span> 
                         in 
                        <span class="font-weight-bold text-primary">
                            {{getSortOrder}}
                        </span>
                         order
                    </p>
                </b-col>
            </b-row>
            <div class="table-responsive">

                 <!--table-->
                <b-table :id="id" 
                         striped hover 
                         class="text-center" 
                         :items="entryData" 
                         :fields="tableFields" 
                         :sort-by.sync="sortBy" 
                         :sort-desc.sync="sortDesc" 
                         @sort-changed="handleSortingChanged">
                    
                    <!--header checkbox slot-->
                    <template slot="HEAD_checkbox" 
                              slot-scope="row">
                        <input type="checkbox" 
                               v-b-tooltip.hover 
                               title="click to select all"
                              class="checkbox" 
                              @click.stop="handleHeaderCheckBoxClick">
                    </template>

                    <!--row checkbox slot-->
                    <template slot="checkbox" 
                              slot-scope="data">
                        <input type="checkbox"
                              class="checkbox">
                    </template>

                    <!--name slot-->
                    <template slot="lastName" 
                              slot-scope="data">
                        {{data.item.lastName}}, {{data.item.firstName}}
                    </template>

                    <!--edit button slot-->
                    <template slot="edit" 
                              slot-scope="data">
                        <b-button size="sm" 
                                  variant="outline-secondary"
                                  @click="onEditButtonClick(data.item)">
                            <i class="fas fa-edit">
                            </i>
                        </b-button>
                    </template>

                    <!--delete button slot-->
                    <template slot="delete" 
                              slot-scope="data">
                        <b-button size="sm" 
                                  variant="outline-danger"
                                  @click="onDelete(data.item.UUID, entryDataOrder, 'single delete', data.item.firstName, data.item.lastName)">
                            <i class="fas fa-trash-alt">
                            </i>
                        </b-button> 
                    </template>
                </b-table>

                 <!--text when no results are found-->
                <p v-if="pagination.total == 0" 
                   class="text-center mt-5 mb-5">
                    No results found. Click 
                     <i class="fas fa-plus"></i> 
                      to add new entry.
                </p>

                 <!--ajax gif-->
                <b-img v-if="entryData.length == 0 && pagination.total != 0" 
                       class="mt-5 mb-5" 
                       center 
                       src="../../../images/ajax-loader.gif" />
            </div>
        </div>

        <!--pagination-->
        <app-pagination :pagination="pagination" 
                        :search="search" 
                        :sortBy="sortBy" 
                        :sortDesc="sortDesc" 
                        :statusFilterSelected="statusFilterSelected"
                        :perPageSelected="perPageSelected"
                        @getRequest="handlePaginationGetRequest($event)">
        </app-pagination>
    </div>
</template>

<script>
    export default {
        props: {
            entryData: {
                type: Array,
                required: true
            },
            tableFields: {
                type: Object, 
                required: true
            },  
            pagination: {
                type: Object,
                required: true
            }, 
            id: {
                type: String, 
                required: true
            }, 
            entryModalId: {
                type: String, 
                required: true
            }
        },

        data(){
            return({
                selectedForDelete: [],

                rowsKept: [],

                entryDataOrder: [],

                perPageSelected: 5,

                statusFilterSelected: 'active',

                 search: '',

                sortBy: 'lastName',

                sortDesc: false,
                
                perPageOptions: [
                    {value: '', text: 'choose entries per page', disabled: true},
                    {value: 5, text: '5 entries per page'},
                    {value: 10, text: '10 entries per page'},
                    {value: 15, text: '15 entries per page'},
                    {value: 20, text: '20 entries per page'}
                ],

                 statusFilterOptions: [
                    {value: '', text: 'choose filter status option', disabled: true},
                    {value: 'active', text:'show active only'},
                    {value: 'inactive', text:'show inactive only'},
                    {value: 'all', text:'show all statuses'}
                ]
            });
        },

        computed: {
            //gets current item that table is being sorted by
            getSortBy: function(){
                if(this.sortBy != null){
                    return this.sortBy == 'lastName' ? 'Name' : this.sortBy.charAt(0).toUpperCase()
                    +this.sortBy.slice(1);
                }else{
                    this.sortBy = 'lastName';
                    return 'Name';
                }
            },
            
            //gets current sort order
            getSortOrder: function(){
                return this.sortDesc ? 'Desc' : 'Asc';
            }
        },

        mounted(){
            this.handleSearch();
        },

        updated(){
            this.handleCheckBoxClick();
            this.findEntryDataOrder();
        },

        methods: {
            //handles data from search input and throttles and debounces the event so the application doesn't crash
            handleSearch(){
                document.getElementById('search')
                .addEventListener('keyup', this.debounce(this.throttle(this.handleFilterInput, 200), 250));
            },
            
            //emits pagination click event and current url w/ perameters to be used for table
            handlePaginationGetRequest(url){
                this.$emit('paginationClick', url);
            },
            
            //emits edit button click and associated entry value from the clicked table row
            onEditButtonClick(person){
                this.$emit('editButtonClick', person);
            },
            
            //emits new button click event to show entry modal 
            onNewButtonClick(){
                this.$emit('newButtonClick');
            },
            
            //finds entry data row order in table based on UUID
            findEntryDataOrder(){
                this.entryDataOrder = [];

                for(let i = 0; i < this.entryData.length; i++){
                    let uuid = this.entryData[i].UUID;
                    this.entryDataOrder.push(uuid);
                }
            },
            
            //emits new url w/ perameters to be used for table based on sorting change
            handleSortingChanged(ctx){
                if(ctx.sortBy != null){
                    this.$emit('filtersChange', this.pagination.path+'?page='+this.pagination.current_page+
                    '&per_page_selected='+this.perPageSelected+'&search='+this.search+'&sort_by='+
                    ctx.sortBy+'&sort_desc='+ctx.sortDesc+'&status_filter_selected='+
                    this.statusFilterSelected);
                }
            },

            //selects all rows if header checkbox is clicked 
            handleHeaderCheckBoxClick(){
                const headerCheckBox = document.querySelector('#'+this.id+' > thead .checkbox');
                const checkBox = document.querySelectorAll('#'+this.id+' > tbody > tr .checkbox'); 
                
                if(headerCheckBox.checked === true){
                    for(let i = 0; i < checkBox.length; i++){
                        checkBox[i].checked = true;
                    }
                }else{
                    for(let i = 0; i < checkBox.length; i++){
                        checkBox[i].checked = false;
                    } 
                }

                this.rowsSelectedOnDelete();
            },
            
            //used to throttle search input event 
            throttle(func, wait, context = this){
                let timeout, callbackArgs;
                
                let later = () => {
                    func.apply(context, callbackArgs);
                    timeout = null;
                }
                
                return function() {
                    if (!timeout) {
                        callbackArgs = arguments;
                        timeout = setTimeout(later, wait);
                    }
                }
            },
            
            //used to debounce search input event
            debounce(func, wait, context = this){
                let timerId, callbackArgs = arguments;

                return function(){
                    if(timerId){
                        clearTimeout(timerId);
                    }

                    timerId = setTimeout(()=>func.apply(context, callbackArgs), wait);
                }

            },

           //emits new url w/ perameters to be used for table based on filter change
           handleFilterInput(){
               this.$emit('filtersChange', this.pagination.path+'?per_page_selected='+
               this.perPageSelected+'&search='+this.search+'&sort_by='+this.sortBy+
               '&sort_desc='+this.sortDesc+'&status_filter_selected='+
               this.statusFilterSelected);
            }, 

            //each row checked is selected for delete
            handleCheckBoxClick(){
                const checkbox = document.querySelectorAll('#'+this.id+' > tbody > tr .checkbox');
               
                for(let i = 0; i < checkbox.length; i++){
                    checkbox[i].addEventListener('click', this.rowsSelectedOnDelete);
                } 
            },
            
            /*Handles checked rows and unchecked rows. Rows checked will be deleted on mass delete. 
            Rows unchecked will be kept and used to updata the entryData array.*/
            rowsSelectedOnDelete(){
                const checkbox = document.querySelectorAll('#'+this.id+' > tbody .checkbox');
                let uuid = '';

                this.selectedForDelete = [];
                this.rowsKept = [];
                
                for(let i = 0; i < this.entryData.length; i++){
                    if(checkbox[i].checked === true){
                        let uuid = this.entryData[i].UUID
                        this.selectedForDelete.push(uuid);
                    }else{
                        this.rowsKept.push(this.entryData[i]);
                    }
                }
            },
            
            //converts string to array
            convertToArray(item){
               return (item.search(',') != -1) ? item.split(',') : new Array(item);
            },
            
            /*workaound for transitioning deleted rows becuse of issuses with transition-group
            component with tables*/ 
            transitionDeletedRows(id_set, table_keys, delete_type){
                const peopleTableRows = document.querySelectorAll('#'+this.id+' > tbody > tr');
                const headerCheckBox = document.querySelector('#'+this.id+' > thead .checkbox');
                const checkBox = document.querySelectorAll('#'+this.id+' > tbody > tr .checkbox'); 
                
                /*Catches any errors from the functions. Only using console log and not alert 
                because none of these functions will crash the application*/
                try{
                    id_set = this.convertToArray(String(id_set));
                    
                    this.setDeleteRowsAttribute(id_set, table_keys, peopleTableRows);
                    this.updateEntryData(id_set, delete_type);
                    this.removeDeleteRowsAttribute(id_set, table_keys, peopleTableRows, headerCheckBox, checkBox);
                    this.selectedForDelete = [];
                }
                catch(err){
                    console.log('Transition delete error: '+err);
                }
            },

            //adds deleted-row class to deleted rows
             setDeleteRowsAttribute(id_set, table_keys, peopleTableRows){
                for(let i = 0; i < id_set.length; i++){
                    let row = table_keys.findIndex(table_keys => table_keys == id_set[i]);
                    peopleTableRows[row].setAttribute('class', 'deleted-row');
                }
            },
            
            //removes deleted rows from view by updating entry array
            updateEntryData(id_set, delete_type){
                setTimeout(()=>{
                    if(delete_type == 'mass delete'){
                        this.$emit('updateEntryData', this.rowsKept);
                    }else{
                        let array = this.entryDataOrder;
                        let entryDataIndex = array.findIndex(array => array == id_set);

                        this.entryData.splice(entryDataIndex, 1);
                    }
                    
                    //finds the new row order by id
                    this.findEntryDataOrder();
                }, 300);
            },
            
            /*Removes the deleted-row class. Neccessary to remove class even though data is deleted
            because page does not refresh.
            
            EX. tableRow[1] may have different data. but if the delete-row class was added, the class
            needs to be removed because the page has not refreshed.*/
            removeDeleteRowsAttribute(id_set, table_keys, peopleTableRows, headerCheckBox, checkBox){
                setTimeout(()=>{
                    headerCheckBox.checked = false;

                    for(let i = 0; i < id_set.length; i++){
                        let row = table_keys.findIndex(table_keys => table_keys == id_set[i]);
                        peopleTableRows[row].removeAttribute('class', 'deleted-row');
                        checkBox[row].checked = false;
                    }

                    for(let i = 0; i < this.entryData.length; i++){
                        checkBox[i].checked = false;
                    }
                }, 300);
            },

            /*handles delete for table rows and it emits object containing neccessary 
            information to handle delete*/
            onDelete(id, table_keys, delete_type, first_name = '', last_name = ''){
                
                /*creates confirm message based on number of entries that are selected 
                and whether or not mass delete button or single button is clicked*/
                const theseOrThis = (id.length > 1) ? 'these ' : 'this ';
                const itemsOrItems = (id.length > 1) ? ' items' : ' item';

                const singleMessage = last_name+', '+first_name;
                const itemsMessage = theseOrThis+id.length+itemsOrItems;
                const deleteItemsMessage = id.length+itemsOrItems;

                const confirmMessage = (first_name != '') ? 'Are you sure you want to delete '+
                singleMessage+'?' : 'Are you sure you want to delete '+itemsMessage+'?';

                this.$emit('deleteEntry', {
                    idSet: id, 
                    firstName: first_name, 
                    lastName: last_name,
                    confirmMessage: confirmMessage,
                    transitionDeletedRows: ()=> this.transitionDeletedRows(id, table_keys, delete_type)
                });
            }
        }
    }
</script>