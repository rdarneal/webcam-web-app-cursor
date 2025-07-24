import './bootstrap';
import '../css/app.css';

import { createApp } from 'vue';
import WebcamApp from './components/WebcamApp.vue';

const app = createApp(WebcamApp);
app.mount('#app');

console.log('WebcamApp mounted');
