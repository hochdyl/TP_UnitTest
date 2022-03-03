import { createApp } from "vue";
import App from "./App.vue";
import axios from "axios";
import VueAxios from "vue-axios";
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import "@fortawesome/fontawesome-free/css/all.css";
import "@fortawesome/fontawesome-svg-core/styles.css";
import popper from "popper.js";
import jquery from "jquery";
import lodash from "lodash";
import "admin-lte";
import { Form } from 'vform';

window.Form = Form;
window._ = lodash;
window.Popper = popper.default;
window.$ = window.jQuery = jquery;

createApp(App).use(VueAxios, axios).mount("#app");