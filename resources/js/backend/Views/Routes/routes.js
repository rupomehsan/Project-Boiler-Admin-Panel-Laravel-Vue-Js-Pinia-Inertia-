//app layout
import Layout from "../Layouts/Layout.vue";
//Dashboard
import Dashboard from "../Management/Dashboard/Dashboard.vue";
//SettingsRoutes
import SettingsRoutes from "../Management/Settings/setup/routes.js";
//UserManagementRoutes
import UserRoutes from "../Management/UserManagement/User/setup/routes.js";
import UserRoleRoutes from "../Management/UserManagement/Role/setup/routes.js";
//routesimport BlogRoutes from '../Management/BlogManagement/Blog/setup/routes.js';
import BlogWriterRoutes from '../Management/BlogManagement/BlogWriter/setup/routes.js';
import BlogTagRoutes from '../Management/BlogManagement/BlogTag/setup/routes.js';
import BlogCategoryRoutes from '../Management/BlogManagement/BlogCategory/setup/routes.js';



const routes = {
  path: "",
  component: Layout,
  children: [
    {
      path: "dashboard",
      component: Dashboard,
      name: "adminDashboard",
    },
    //management routes        BlogRoutes,
        BlogWriterRoutes,
        BlogTagRoutes,
        BlogCategoryRoutes,


   

    //user routes
    UserRoutes,
    UserRoleRoutes,
    //settings
    SettingsRoutes,
  ],
};

export default routes;
