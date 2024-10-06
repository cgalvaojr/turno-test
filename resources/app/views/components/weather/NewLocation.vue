<template>
    <Page>
        <div class="max-w-md mx-auto p-6 bg-white border border-gray-200 rounded-lg shadow-md">
            <h2 class="text-lg font-semibold mb-4">Create New Location</h2>
            <div class="mb-4">
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input v-model="country" maxlength="2" type="text" id="country" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                <p v-if="isSubmitted && !isCountryValid" class="text-red-500 text-sm mt-1">Country is required.</p>
            </div>
            <div class="mb-4">
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input v-model="city" type="text" id="city" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                <p v-if="isSubmitted && !isCityValid" class="text-red-500 text-sm mt-1">City is required.</p>
            </div>
            <div class="flex justify-around">
                <button
                    :class="{'bg-blue-500 hover:bg-blue-600': !isSaving, 'bg-gray-500': isSaving}"
                    class="text-white px-4 py-2 rounded"
                    @click="saveLocation"
                    :disabled="isSaving">
                    <span>Save Location</span>
                </button>
            </div>
        </div>
    </Page>
</template>

<script>
import { defineComponent, ref, computed } from 'vue';
import Page from "@/views/layouts/Page";
import WeatherService from "@/services/WeatherService";
import { useRouter } from 'vue-router';
import {useAlertStore} from "@/stores";

export default defineComponent({
    components: {
        Page,
    },
    setup() {
        const country = ref('');
        const city = ref('');
        const isSaving = ref(false);
        const isSubmitted = ref(false);
        const weatherService = new WeatherService();
        const router = useRouter();

        const isCountryValid = computed(() => country.value.trim() !== '');
        const isCityValid = computed(() => city.value.trim() !== '');
        const isFormValid = computed(() => isCountryValid.value && isCityValid.value);

        const alertStore = useAlertStore();
        alertStore.$reset();

        async function saveLocation() {
            isSubmitted.value = true;
            if (!isFormValid.value) return;
            isSaving.value = true;
            try {
                await weatherService.storeLocation({ country: country.value, city: city.value });
                await router.push('/location');
            } catch (error) {
                alertStore.error(error.response.data.message);
            } finally {
                isSaving.value = false;
            }
        }

        return {
            country,
            city,
            saveLocation,
            isSaving,
            isSubmitted,
            isCountryValid,
            isCityValid,
            isFormValid,
            alertStore
        }
    }
});
</script>

<style scoped>
</style>
