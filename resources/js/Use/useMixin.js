import moment from 'moment'
import 'moment/min/locales'

export default {
    methods: {
        /**
         * @param key
         * @param replace
         * @returns {string}
         */
        __(key, replace = {}) {

            // If locale is hu then replace hu.json key:value
            if (this.$page.props.locale == 'hu') {
                var translation = this.$page.props.language[key]
                    ? this.$page.props.language[key]
                    : key

                Object.keys(replace).forEach(function (key) {
                    translation = translation.replace(':' + key, replace[key])
                });

                return translation
            }

            // Key is the english
            return key;
        },

        trans(key, replace = {}) {
            return this.__(key, replace);
        },

        /**
         * @param date
         * @param format
         * @returns {string}
         */
        dateFormat(date, format = null) {
            //moment.locale('hu')
            return moment(date)
                .locale(this.$page.props.locale ?? 'hu')
                .format(format ? format : this.$page.props.auth.user.date_time_format)
        },

        getVersion() {
            return this.$page.props.version;
        },

        /**
         * @param url
         * @returns {boolean}
         */
        isUrl(url) {
            return this.$inertia.page.url.startsWith(url)
        },

        /**
         * @param roles
         * @returns {*|boolean}
         */
        hasRole(...roles) {
            let user = this.$page.props.auth.roles;

            if (user.includes('superadmin')) {
                return true
            }

            return user.some(u => roles.includes(u))
        },

        /**
         * @param permission
         * @returns {boolean}
         */
        hasPermission(permission) {
            // superadmin or admin above all permissions ?
            if (this.hasRole('superadmin') || this.hasRole('admin')) {
                return true
            }
            return this.$page.props.auth.permissions.includes(permission)
        },

        /**
         * @param profile_photo_url
         * @param name
         * @returns {string|*}
         */
        getProfPicture(profile_photo_url, name) {
            if (!profile_photo_url) {
                return 'https://ui-avatars.com/api/?name=' + name + '&color=FFF&background=' + this.removeHastag(this.$vuetify.theme.themes.light.primary);
            }

            return profile_photo_url
        },

        trim(string, length = 50) {
            return string && string.length > length
                ? string.substring(0, length - 3) + "..."
                : string;
        },

        removeHastag(string) {
            return string.substring(1);
        },

        refreshPage() {
            this.$inertia.reload()
        },

        route: window.route
    }
}
