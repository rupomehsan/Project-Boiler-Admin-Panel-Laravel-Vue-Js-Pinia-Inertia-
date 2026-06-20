import setup from ".";
import All from "../pages/All.vue";
import Form from "../pages/Form.vue";
import Details from "../pages/Details.vue";
import Layout from "../pages/Layout.vue";

let route_prefix = setup.route_prefix;
let route_path = setup.route_path;

const routes = {
    path: route_path,
    component: Layout,
    children: [
        {
            path: "all",
            name: "All" + route_prefix,
            component: All,
            meta: { permission: setup.permission_slugs?.view },
        },
        {
            path: "create",
            name: "Create" + route_prefix,
            component: Form,
            meta: { permission: setup.permission_slugs?.create },
        },
        {
            path: "details/:id",
            name: "Details" + route_prefix,
            component: Details,
            meta: { permission: setup.permission_slugs?.details },
        },
        {
            path: "edit/:id",
            name: "Edit" + route_prefix,
            component: Form,
            meta: { permission: setup.permission_slugs?.edit },
        },
    ],
};

export default routes;
