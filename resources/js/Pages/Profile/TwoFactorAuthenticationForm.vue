<template>
    <form-section>
        <template #title>
            {{trans('Two Factor Authentication')}}
        </template>

        <template #description>
            {{trans('Add additional security to your account using two factor authentication.')}}
        </template>

        <template #form>
            <h3 class="text-base" v-if="twoFactorEnabled">
                {{trans('You have enabled two factor authentication.')}}
            </h3>

            <h3 class="text-base" v-else>
                {{trans('You have not enabled two factor authentication.')}}
            </h3>

            <div>
                <p class="text-base">
                    {{trans("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.")}}
                </p>
            </div>

            <div v-if="twoFactorEnabled">
                <div v-if="qrCode">
                    <div class="mt-4">
                        <p class="text-base">
                            {{trans("Two factor authentication is now enabled.Scan the following QR code using your phone's authenticator application.")}}
                        </p>
                    </div>

                    <div class="mt-4" v-html="qrCode">
                    </div>
                </div>

                <div v-if="recoveryCodes.length > 0">
                    <div>
                        <p class="text-base font-semibold">
                            {{trans("Store these recovery codes in a secure password manager.They can be used to recover access to your account if your two factor authentication device is lost.")}}
                        </p>
                    </div>

                    <div>
                        <ul class="text-base" v-for="code in recoveryCodes" :key="code">
                            <li>{{ code }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </template>

        <template #actions>
            <div v-if="! twoFactorEnabled">
                <ConfirmsPassword @confirmed="enableTwoFactorAuthentication">
                    <v-btn type="button" color="primary"  :loading="enabling">
                        {{trans('Enable')}}
                    </v-btn>
                </ConfirmsPassword>
            </div>

            <div v-else>
                <v-row class="col">
                    <ConfirmsPassword @confirmed="regenerateRecoveryCodes">
                        <v-btn
                            type="button"
                            color="secondary" 
                            v-if="recoveryCodes.length > 0">
                            {{trans('Regenerate Recovery Codes')}}
                        </v-btn>
                    </ConfirmsPassword>
                    <ConfirmsPassword @confirmed="showRecoveryCodes">
                        <v-btn type="button" color="primary"  v-if="recoveryCodes.length === 0">
                            {{trans('Show Recovery Codes')}}
                        </v-btn>
                    </ConfirmsPassword>

                    <ConfirmsPassword @confirmed="disableTwoFactorAuthentication">
                        <v-btn
                            class="ml-2"
                            color="error" 
                            :loading="disabling">
                            {{trans('Disable')}}
                        </v-btn>
                    </ConfirmsPassword>
                </v-row>
            </div>
        </template>
    </form-section>
</template>

<script>
import FormSection from '@/Components/Shared/FormSection.vue'
import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";

export default {
    components: {
        FormSection,
        ConfirmsPassword
    },

    data() {
        return {
            enabling: false,
            disabling: false,

            qrCode: null,
            recoveryCodes: [],
        }
    },

    methods: {
        enableTwoFactorAuthentication() {
            this.enabling = true

            this.$inertia.post(this.route('two-factor.enable'), {}, {
                preserveScroll: true,
                onSuccess: () => Promise.all([
                    this.showQrCode(),
                    this.showRecoveryCodes(),
                ]),
                onFinish: () => (this.enabling = false),
            })
        },

        showQrCode() {
            return axios.get(this.route('two-factor.qr-code'))
                .then(response => {
                    this.qrCode = response.data.svg
                })
        },

        showRecoveryCodes() {
            return axios.get(this.route('two-factor.recovery-codes'))
                .then(response => {
                    this.recoveryCodes = response.data
                })
        },

        regenerateRecoveryCodes() {
            axios.post(this.route('two-factor.recovery-codes.submit'))
                .then(response => {
                    this.showRecoveryCodes()
                })
        },

        disableTwoFactorAuthentication() {
            this.disabling = true

            this.$inertia.delete(this.route('two-factor.disable'), {
                preserveScroll: true,
                onSuccess: () => (this.disabling = false),
            })
        },
    },

    computed: {
        twoFactorEnabled() {
            return ! this.enabling && this.$page.props.auth.user.two_factor_enabled
        }
    }
}
</script>
