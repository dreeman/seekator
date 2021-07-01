/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
import Vue from 'vue';

require('./bootstrap');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component('video-form', require('./components/VideoForm.vue').default);
Vue.component('video-list', require('./components/VideoList.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app',
     data: {
        messages: [],
        users: [],
        files: [],
        alert: {
            type: null,
            content: '',
        },
    },

    mounted() {
        this.checkStatus();

        Echo.private('App.Models.User.' + document.getElementById('app').getAttribute('data-userid').trim())
            .listen('MessageSent', (event) => {
                if (event.message.message === 'process_done') {
                    this.files = this.prepareFiles(event.message.files);
                }
            });
    },

    methods: {
        checkStatus() {
            axios.get('/api/check').then(response => {
                if (response.status === 200) {
                    this.files = this.prepareFiles(response.data.data.files);
                }
            });
        },

        sendForm(data) {
            axios.post('/api/run', data).then(response => {
                this.showAlert(response.data.data.message, 'success');
            }).catch(error => {
                this.showAlert(error.response.data.errors, 'error');
            });
        },

        showAlert(content, type = 'success') {
            this.alert = {type, content};
        },

        hideAlert() {
            this.alert = {type: null, content: ''};
        },

        prepareFiles(files) {
            return files.map(file => {
                const bages = [{}, {type: 'bg-warning', text: 'Queued'}, {type: 'bg-success', text: 'In progress'}, {type: 'bg-danger', text: 'Failed'}];
                return {
                    status: file.status,
                    thumb: 'https://img.youtube.com/vi/' + file.vendor_code + '/0.jpg',
                    link: file.link,
                    title: (file.status === 0) ? file.title : file.vendor_code,
                    bage: bages[file.status],
                };
            });
        },
    },
});
