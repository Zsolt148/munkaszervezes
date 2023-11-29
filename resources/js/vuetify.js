import Vue from "vue";
import Vuetify from "vuetify";
import "vuetify/dist/vuetify.min.css";
import "@mdi/font/css/materialdesignicons.css";
Vue.use(Vuetify);

const options = {
    theme: {
        themes: {
            light: {
                primary: '#1976D2',
                secondary: '#424242',
                accent: '#82B1FF',
                error: '#FF5252',
                info: '#2196F3',
                success: '#4CAF50',
                warning: '#FFC107',
                background: '#f3f3f3',
                appbar: '#fff'
            },
            dark: {
                primary: '#1976D2',
                background: '#3a3a3a',
                appbar: '#272727'

            },
        },
    },
    icons: {
        iconfont: "mdi", // default - only for display purposes
    },
};

export default new Vuetify(options);
