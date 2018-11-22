<template>
    <!--processing gif container-->
    <div v-if="processing"
         id="processing-container">

         <!--gif-->
        <b-img center 
              :src="imgSrc" />
        
        <!--processing gif text-->
        <h4 class="text-center text-white mt-4">
            {{loaderText}}
        </h4>
    </div>
</template>

<script>
    export default {
        props: {
            processing: {
                type: Boolean, 
                required: true
            },

            imgSrc: {
                type: String, 
                required: true
            },

            loaderText: {
                type: String, 
                default: ''
            }
        },

        beforeUpdate(){
            this.scrollToTopAndLeft();
        },

        methods: {
            //makes scroll top and left 0 so there is no white space showing when gif container shows 
            scrollToTopAndLeft(){
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;
                document.documentElement.scrollLeft = 0;
                document.body.scrollLeft = 0;
            }
        }
    }
</script>
