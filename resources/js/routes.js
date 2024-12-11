import { createRouter, createWebHistory } from "vue-router";

import Index from "./components/visits/Index.vue";
import Maps from "./components/maps/Index.vue";
import User from "./components/users/Index.vue";

const routes = [
    {
        path: "/",
        component: Index,
    },
    {
        path: "/maps",
        name: "maps",
        component: Maps,
    },
    {
        path: "/users",
        name: "users",
        component: User,
    }
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});
export default router;
