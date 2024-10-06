<template>
    <Page>
        <div>
            <h2 class="text-lg font-semibold mb-4">Weather Details</h2>
            <h3 class="text-lg font-semibold mb-4">{{ weatherLocations.city }} - {{ weatherLocations.country }}</h3>
            <button @click="goBack" class="px-4 py-2 bg-blue-500 text-white rounded">Back</button>


            <table class="min-w-full divide-y divide-gray-200 mt-2">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Temperature</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feels Like</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weather</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Wind Speed</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="weather in weatherLocations.weathers" :key="weather.id">
                        <td class="px-6 py-4 whitespace-nowrap">{{ weather.dt_txt }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ weather.temp }} °F</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ weather.feels_like }} °F</td>
                        <td class="px-6 py-4 whitespace-nowrap"><img :src="`https://openweathermap.org/img/wn/${weather.icon}.png`" :title="weather.description" :alt="weather.description" /></td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ weather.wind_speed }} m/s</td>
                    </tr>
                    </tbody>
                </table>
        </div>
    </Page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import Page from "@/views/layouts/Page";
import WeatherService from "@/services/WeatherService";
import { useRoute, useRouter } from 'vue-router';

export default defineComponent({
    components: {
        Page,
    },
    setup() {
        const weatherLocations = ref([]);
        const route = useRoute();
        const router = useRouter();

        const goBack = () => {
            router.back();
        };

        onMounted(async () => {
            try {
                const response = await new WeatherService().getAllWeatherLocations(route.params.id);
                weatherLocations.value = response.data.data[0];
            } catch (error) {
                console.error("Error fetching weather locations:", error);
            }
        });

        return {
            weatherLocations,
            goBack
        }
    }
});
</script>

<style scoped>
</style>
