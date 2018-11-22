<template>
    
    <!--modal-->
    <b-modal :id="id" 
             :size="size" 
             :title="modalTitle" 
             header-bg-variant="info" 
             header-text-variant="white" 
             :hide-header-close="hideHeaderClose"
             hide-footer 
             v-model="showModal"
             @shown="onShown" 
             @hidden="onHidden">
        
        <!--error alert-->
        <b-alert v-for="(error, index) in modalErrors" 
                 :key="index" 
                 show 
                 variant="danger">
            {{error}}
        </b-alert>

        <!--ajax gif-->
        <b-img v-if="saveInProgress" 
               class="mt-5 mb-5" 
               center 
               src="../../../images/ajax-loader.gif" />
        
        <!--modal body-->
        <div v-else>
            <slot name="body">
            </slot>
        </div>
    </b-modal>  
</template>

<script>
    export default {
        props: {
            id: {
                type: String,
                default: ''
            },
            size: {
                type: String,
                default: ''
            },
            saveInProgress: {
                type: Boolean,
                default: false
            },
            modalTitle: {
                type: String,
                default: ''
            }, 
            modalErrors: {
                type: [Array, Object],
                default: null
            },
            showValue: {
                type: Boolean, 
                required: true
            },
            hideHeaderClose: {
                type: [Boolean], 
                default: false
            }
        },

        data(){
            return({
                showModal: false
            });
        },

        updated(){
            this.setShowModal();
        }, 

        methods: {
             //shows or hides modal based on prop
             setShowModal(){
                this.showModal = this.showValue;
            },
            
            //emits b-modal shown event to be handled from the outside 
            onShown(){
                this.$emit('shown');
            },

            //emits b-modal hidden event to be handled from the outside
            onHidden(){
                this.$emit('hidden');
            }
        }
    }
</script>