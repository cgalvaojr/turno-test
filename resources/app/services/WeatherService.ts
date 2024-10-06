import BaseService from "@/services/BaseService";
import axios from "axios";

export default class UserService extends BaseService {

        constructor() {
            super();
            this.url = import.meta.env.VITE_APP_URL;
            this.setupAPI(axios.defaults.baseURL);
        }

        async getWeather() {
            // await this.get(`/api/weather/city/${city}/country/${country}`);
            return this.get(`/api/weather`);
        }

        async removeLocation(locationId) {
            return this.delete(`/api/weather/${locationId}`);
        }
}
