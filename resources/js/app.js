import { createApp } from 'vue';
import App from './App.vue';
import vuetify from './vuetify'; // Import the configured Vuetify instance
import 'vuetify/styles'; // Vuetify CSS styles


const app = createApp(App);

// Use Vuetify with Vue 3
app.use(vuetify);

// Mount the Vue app
app.mount('#app');
