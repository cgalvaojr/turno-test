<template>
    <Page>
        <div>
            <Modal :is-showing="isRemoveModalShowing" @close="isRemoveModalShowing = false;">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-md">
                    <p class="text-lg font-semibold mb-4">Are you sure you want to proceed?</p>
                    <div class="flex justify-end space-x-2">
                        <button class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600" @click="isRemoveModalShowing = false">Cancel</button>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" @click="handleRemoveLocation">Remove Location</button>
                    </div>
                </div>
            </Modal>

            <div class="flex justify-end mb-4">
                <button class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600" @click="redirectToNewLocation">Create New Location</button>
            </div>

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">City</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="(weather, index) in weatherData" :key="index">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="text-sm font-medium text-gray-900">{{ weather.country }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ weather.city }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900 flex space-x-2">
                            <button class="text-blue-500 hover:text-blue-700">
                                <Icon name="eye" title="See details" />
                            </button>
                            <button class="text-red-500 hover:text-red-700">
                                <Icon name="trash" title="Remove forecast" @click="isRemoveModalShowing = true;currentLocationId = weather.id" />
                            </button>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
            <Spinner v-if="isRemoving" />
            <Alert v-if="errorMessage" :message="errorMessage" type="error" />
        </div>
    </Page>
</template>

<script>
import { defineComponent, ref, onMounted } from 'vue';
import { trans } from "@/helpers/i18n";
import Page from "@/views/layouts/Page";
import Icon from "@/views/components/icons/Icon";
import Modal from "@/views/components/Modal";
import WeatherService from "@/services/WeatherService";
import Spinner from "@/views/components/icons/Spinner.vue";
import Alert from "@/views/components/Alert.vue";
import { useRouter } from 'vue-router';
import {useAlertStore} from "@/stores";


export default defineComponent({
    components: {
        Modal,
        Page,
        Icon,
        Spinner,
        Alert,
    },
    setup() {
        const isRemoveModalShowing = ref(false);
        const currentLocationId = ref(null);
        const isRemoving = ref(false);
        const weatherService = new WeatherService();
        const weatherData = ref([]);
        const errorMessage = ref(null);
        const router = useRouter();
        const alertStore = useAlertStore();
        alertStore.$reset();

        onMounted(async () => {
            try {
                const response = await weatherService.getWeather();
                weatherData.value = response.data.data;
            } catch (error) {
                console.error("Error fetching weather data:", error);
            }
        });

        async function handleRemoveLocation() {
            isRemoving.value = true;
            try {
                await weatherService.removeLocation(currentLocationId.value);
                weatherData.value = weatherData.value.filter(weather => weather.id !== currentLocationId.value);
                isRemoveModalShowing.value = false;
            } catch (error) {
                console.error("Error removing location:", error);
            } finally {
                isRemoving.value = false;
            }
        }

        function redirectToNewLocation() {
            if (weatherData.value.length >= 3) {
                alertStore.error("You cannot add more than 3 locations.");
            } else {
                alertStore.error(null);
                router.push('/location/new');
            }
        }

        return {
            trans,
            weatherData,
            isRemoveModalShowing,
            handleRemoveLocation,
            currentLocationId,
            isRemoving,
            redirectToNewLocation,
            errorMessage,
            alertStore
        }
    }
});
</script>
