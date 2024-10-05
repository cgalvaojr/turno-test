import BaseService from "@/services/BaseService";
import axios from "axios";

export default class UserService extends BaseService {

        constructor() {
            super();
            this.url = import.meta.env.VITE_APP_URL;
            this.setupAPI(axios.defaults.baseURL);
        }

        public async getWeather(city, country) {
            await this.get(`/api/weather/city/${city}/country/${country}`);
        }
}
