import { createApp } from 'vue';
import { createPinia } from 'pinia';
import App from './App.vue';
import router from './router';
import Vue3PersianDatetimePicker from 'vue3-persian-datetime-picker'

const pinia = createPinia();
const app = createApp(App).use(pinia).use(router);

app.component('DatePicker', Vue3PersianDatetimePicker);
app.mount('#app');