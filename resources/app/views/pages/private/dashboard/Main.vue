<template>
    <Page>
        <div class="w-1/2 m-auto text-center pt-10">
            <h1 class="text-6xl mb-4 font-bold text-gray-600">Welcome</h1>
            <h4 class="text-gray-500 text-2xl">{{ new Date() }}</h4>
        </div>
        <footer class="py-5 text-center">
            <p class="text-gray-400">{{ trans('global.phrases.inspire') }}</p>
        </footer>
    </Page>
</template>

<script>
import {defineComponent} from 'vue';
import {trans} from "@/helpers/i18n";
import Page from "@/views/layouts/Page";
import WeatherService from "@/services/WeatherService";

export default defineComponent({
    components: {
        Page,
    },
    actions: {},
    setup() {
        const weatherService = new WeatherService();

        console.log(weatherService.getWeather('teste', '123'));
        return {
            trans,
            weatherService
        }
    }
});
</script>
