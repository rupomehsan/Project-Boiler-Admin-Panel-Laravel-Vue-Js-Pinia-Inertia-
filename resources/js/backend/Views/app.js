// setup  files
// setup  files
import "./bootstrap.js";
import "../plugins/axios_setup.js";
import "../plugins/sweet_alert.js";
import "../plugins/moment_setup.js";
import "../plugins/number_to_text.js";
// CSS imports
import '@fortawesome/fontawesome-free/css/all.min.css';

// Root components
// Root components
import { createApp } from "vue";
import { createRouter, createWebHashHistory } from "vue-router";
import { createPinia } from "pinia";
import App from "./Layouts/App.vue";
// common components
// common components
import CommonInput from "../GlobalComponents/FormComponents/CommonInput.vue";
import Pagination from "../GlobalComponents/Pagination.vue";
// project rotes
// project rotes
import Routes from "./Routes/routes.js";
import { auth_store } from "../GlobalStore/auth_store";
// roters
// roters
const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: "/",
      component: App,
      children: [Routes],
    },
  ],
});
// previous route store
// previous route store
router.beforeEach(async (to, from, next) => {
  to.href.length > 2 && window.sessionStorage.setItem("prevurl", to.href);

  const requiredPermission = to.meta?.permission;
  if (requiredPermission) {
    const authStore = auth_store();
    if (!authStore.is_auth) {
      await authStore.check_is_auth();
    }
    if (!authStore.has_permission(requiredPermission)) {
      return next(false);
    }
  }

  next();
});

// render component
// render component
const pinia = createPinia();
const app = createApp({});

app.component("app", App);
app.component("common-input", CommonInput);
app.component("pagination", Pagination);

app.use(pinia);
app.use(router);
app.mount("#app");
