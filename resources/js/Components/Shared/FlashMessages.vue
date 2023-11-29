<template>
    <v-snackbar
        v-model="snackbar"
        :timeout="3000"
        bottom
        right
    >
        {{ text }}
        <template v-slot:action="{ attrs }">
            <v-btn
                color="white"
                text
                v-bind="attrs"
                @click="snackbar = false"
            >
                <v-icon>mdi-close</v-icon>
            </v-btn>
        </template>
    </v-snackbar>
</template>

<script>
export default {
    name: 'FlashMessages',
    props: ['flash', 'errors'],
    mounted() {
        // to work at redirects
        this.showFlash()
    },
    data() {
        return {
            timeout: null,
            snackbar: false,
            color: 'black',
        }
    },
    computed: {
        text() {
            if (this.flash.success) {
                return this.flash.success
            }

            if (this.flash.error) {
                return this.flash.error
            }

            if (this.errors && this.errors.length) {
                return this.trans('There is an incorrect field')
            }
        }
    },
    methods: {
        showFlash() {
            if (this.flash.success || this.flash.error) {
                if (this.timeout) {
                    clearTimeout(this.timeout);
                }

                //this.color = this.flash.success ? '#50bc00' : '#ff4c51'
                this.snackbar = true

                // clear props
                this.timeout = setTimeout(() => {
                    this.$page.props.flash.success = null;
                    this.$page.props.flash.error = null;
                }, 3500)
            }
        }
    },
    watch: {
        'errors': {
            handler() {
                // this.color = '#ff4c51'
                // this.snackbar = true
            },
            deep: true,
        },
        'flash': {
            handler() {
                this.showFlash()
            },
            deep: true,
        },
    },
}
</script>
