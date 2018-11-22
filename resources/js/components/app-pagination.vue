<template>
    <!--pagination navigation-->
    <nav aria-label="Page navigation" 
         class="navbar">
        <ul class="pagination pagination-md">

            <!--pagination prev link-->
            <li :class="{disabled: !pagination.prev_page_url}" 
                class="page-item">
                <a class="page-link"
                   href="#" 
                   :tabindex="findTabIndex(pagination.prev_page_url)" 
                   aria-label="Prev" 
                   @click.prevent="handlePaginationClick(pagination.prev_page_url+urlData)">
                    <span aria-hidden="true">
                        &laquo; Prev
                    </span>
                    <span class="sr-only">
                        Prev
                    </span>
                </a>
            </li>

            <!--pagination number links-->
            <li v-for="link in pagination.links" 
                :class="findPaginationClasses(link.isActiveLink, link.href)" 
                class="page-item">
                <a class="page-link"
                   :tabindex="findTabIndex(link.href)" 
                   href="#" 
                   @click.prevent="handlePaginationClick(link.href+urlData)">
                    {{link.number}}
                </a>
            </li>

            <!--pagination next link-->
            <li :class="{disabled: !pagination.next_page_url}"
                class="page-item">
                <a class="page-link" 
                   href="#" 
                   :tabindex="findTabIndex(pagination.next_page_url)" 
                   aria-label="Next" 
                   @click.prevent="handlePaginationClick(pagination.next_page_url+urlData)">
                    <span aria-hidden="true">
                        Next &raquo;
                    </span>
                    <span class="sr-only">
                        Next
                    </span>
                </a>
            </li>
        </ul>

        <!--from and to text-->
        <span class="navbar-text">
            Showing {{pagination.from}} to {{pagination.to}} of {{pagination.total}} entries
        </span>
    </nav>
</template>
    
<script>
    export default {
        props: {
            pagination: {
                type: Object,
                required: true
            },
            search: {
                type: String,
                default: ''
            },
            sortBy: {
                type: String,
                default: ''
            },
            sortDesc: {
                type: Boolean,
                default: ''
            }, 
            perPageSelected: {
                type: [Number, String],
                default: ''
            },
            statusFilterSelected: {
                type: String,
                default: ''
            }
        }, 

        data(){
            return ({
                urlData: ''
            });
        },

        updated(){
            this.setUrlData();
        },

        methods: {
            //sets url data everytime pagination updates
            setUrlData(){
                this.urlData = '&per_page_selected='+this.perPageSelected+
                '&search='+this.search+'&sort_by='+this.sortBy+'&sort_desc='+this.sortDesc+
                '&status_filter_selected='+this.statusFilterSelected;
            },

            //emits url value everytime pagination is clicked 
            handlePaginationClick(page_url){
                this.$emit('getRequest', page_url);
            }, 

            //disables anchor tag if there is no url
            findTabIndex(page_url){
                let tabIndex = (page_url == '') ? '' : '-1';
                return tabIndex;
            }, 
            
            //determines whether pagination link is active or if its disabled.
            findPaginationClasses(is_active, page_url){
                return ({
                    active: is_active, 
                    disabled: !page_url
                });
            }
        }
    }
</script>