import './bootstrap';

import { createApp } from 'vue';
import App from './App.vue';
// import MapComponent from './components/MapComponent.vue';
const app = createApp(App);
import router from './router/index';

// app.component('map-component', MapComponent);
app.use(router);
app.mount('#app');
