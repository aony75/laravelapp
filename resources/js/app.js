/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue';
import BootstrapVue from 'bootstrap-vue';

Vue.use(BootstrapVue);

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */


Vue.component('staff-table', require('./components/staff-table.vue'));
Vue.component('app-pagination', require('./components/app-pagination.vue'));
Vue.component('staff-content', require('./components/staff-content.vue'));
Vue.component('app-modal', require('./components/app-modal.vue'));
Vue.component('login-page', require('./components/login-page.vue'));
Vue.component('register-page', require('./components/register-page.vue'));
Vue.component('processing-gif', require('./components/processing-gif.vue'));



const app = new Vue({
    el: '#app'
});
