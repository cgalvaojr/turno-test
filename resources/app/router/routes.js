import {default as PageLogin} from "@/views/pages/auth/login/Main";
import {default as PageNotFound} from "@/views/pages/shared/404/Main";
import {default as PageDashboard} from "@/views/components/weather/Weather.vue";
import NewLocation from "@/views/components/weather/NewLocation.vue";
import ShowLocationWeather from "@/views/components/weather/ShowLocationWeather.vue";

const routes = [
    {
        name: "home",
        path: "/",
        meta: {requiresAuth: false, isPublicAuthPage: true},
        component: PageLogin,
    },
    {
        name: "panel",
        path: "/location",
        children: [
            {
                name: "list",
                path: "",
                meta: {requiresAuth: true},
                component: PageDashboard,
            },
            {
                name: "new",
                path: "new",
                meta: {requiresAuth: true, isOwner: true},
                component: NewLocation,
            },
            {
                name: "show",
                path: ":id",
                meta: {requiresAuth: true, isOwner: true},
                component: ShowLocationWeather,
            },
        ]
    },
    {
        path: "/login",
        name: "login",
        meta: {requiresAuth: false, isPublicAuthPage: true},
        component: PageLogin,
    },
    {
        path: "/:catchAll(.*)",
        name: "notFound",
        meta: {requiresAuth: false},
        component: PageNotFound,
    },
]

export default routes;
