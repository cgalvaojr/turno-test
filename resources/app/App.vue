<template>
    <div class="bg-gray-100 flex" v-if="authStore.user && authStore.user.hasOwnProperty('id')">
        <aside class="relative bg-theme-600 h-screen w-64 hidden sm:block shadow-xl">
            <div class="p-6 border-b border-theme-600">
                <router-link class="text-white text-3xl font-semibold uppercase hover:text-gray-300" to="/location">
                    <template v-if="state.app.logo">
                        <img :src="state.app.logo" :alt="state.app.name"/>
                    </template>
                    <template v-else>
                        {{ state.app.name }}
                    </template>
                </router-link>
            </div>
            <nav class="text-white text-base py-4 px-3 rounded">
                <Menu :state="state" :type="'desktop'"/>
            </nav>
        </aside>
        <div class="relative w-full flex flex-col h-screen overflow-y-hidden">
            <!-- Desktop Header -->
            <header class="w-full items-center bg-white py-2 px-6 hidden sm:flex">
                <div class="w-1/2"></div>
                <div class="relative w-1/2 flex justify-end">
                    <a class="flex cursor-pointer focus:outline-none align-middle" @click="state.isAccountDropdownOpen = !state.isAccountDropdownOpen">
                        <span class="relative pt-3 mr-2">{{ authStore.user.full_name }} <Icon :name="state.isAccountDropdownOpen ? 'angle-up' : 'angle-down'"/></span>
                        <button class="relative z-10 w-12 h-12 rounded-full overflow-hidden border-4 border-gray-400 hover:border-gray-300 focus:border-gray-300 focus:outline-none">
                            <img :alt="authStore.user.full_name" v-if="authStore.user.avatar_thumb_url" :src="authStore.user.avatar_thumb_url">
                            <AvatarIcon v-else/>
                        </button>
                    </a>
                    <button v-if="state.isAccountDropdownOpen" @click="state.isAccountDropdownOpen = false" class="h-full w-full fixed inset-0 cursor-pointer"></button>
                    <div v-if="state.isAccountDropdownOpen" class="absolute w-32 bg-white rounded-lg shadow-lg py-2 mt-16 z-50">
                        <a href="#" @click.prevent="onLogout" class="block px-4 py-2 hover:bg-theme-800 hover:text-white hover:opacity-80">
                            {{ trans('global.phrases.sign_out') }}
                        </a>
                    </div>
                </div>
            </header>

            <!-- Mobile Header & Nav -->
            <header class="w-full bg-theme-600 py-5 px-6 sm:hidden">
                <div class="flex items-center justify-between">
                    <router-link class="text-white text-3xl font-semibold uppercase hover:text-gray-300" to="/location">
                        {{ state.app.name }}
                    </router-link>
                    <button @click="state.isMobileMenuOpen = !state.isMobileMenuOpen" class="text-white text-3xl focus:outline-none">
                        <i v-if="!state.isMobileMenuOpen" class="fa fa-bars"></i>
                        <i v-else class="fa fa-times"></i>
                    </button>
                </div>
            </header>

            <div class="w-full h-screen overflow-x-hidden border-t flex flex-col">
                <main class="w-full flex-grow p-6">
                    <router-view/>
                </main>
                <footer class="w-full bg-white text-center text-sm p-4" v-html="trans('global.phrases.copyright')"></footer>
            </div>

        </div>
    </div>
    <template v-else>
        <router-view/>
    </template>
</template>

<script>
import {computed, onBeforeMount, reactive} from "vue";

import {trans} from '@/helpers/i18n';
import Menu from "@/views/layouts/Menu";
import Icon from "@/views/components/icons/Icon";
import AvatarIcon from "@/views/components/icons/Avatar";
import {useAuthStore} from "@/stores/auth";
import {useGlobalStateStore} from "@/stores";
import {useRoute} from "vue-router";
import {useAlertStore} from "@/stores";

export default {
    name: "app",
    components: {
        AvatarIcon,
        Menu,
        Icon
    },
    setup() {

        const alertStore = useAlertStore();
        const authStore = useAuthStore();
        const globalStateStore = useGlobalStateStore();
        const route = useRoute();

        const isLoading = computed(() => {
            let value = false;
            for(let i in globalStateStore.loadingElements) {
                if(globalStateStore.loadingElements[i]){
                    value = true;
                    break;
                }
            }
            return value || globalStateStore.isUILoading;
        })

        const state = reactive({
            mainMenu: [
                {
                    name: trans('global.pages.home'),
                    icon: 'sun',
                    showDesktop: true,
                    showMobile: true,
                    requiresAbility: false,
                    to: '/location',
                },
                {
                    name: trans('global.phrases.sign_out'),
                    icon: 'sign-out',
                    showDesktop: false,
                    showMobile: true,
                    showIfRole: false,
                    onClick: onLogout,
                    to: '',
                }
            ],
            isAccountDropdownOpen: false,
            isMobileMenuOpen: false,
            currentExpandedMenuItem: null,
            app: window.AppConfig,
        });

        function onLogout() {
            authStore.logout()
        }

        onBeforeMount(() => {
            if (route.query.hasOwnProperty('verified') && route.query.verified) {
                alertStore.success(trans('global.phrases.email_verified'));
            }
        });

        return {
            state,
            authStore,
            globalStateStore,
            trans,
            onLogout,
            isLoading,
        }
    }
};
</script>
