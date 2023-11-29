<template>
    <auth-layout>
        <AppHead :title="trans('Two factor challange')" />

        <v-layout align-center justify-center>
            <div class="auth-wrapper auth-v1">
                <div class="auth-inner">
                    <v-card elevation="4" class="auth-card">

                        <v-card-text>
                            <p v-if="! recovery">
                                {{ trans("Please confirm access to your account by entering the authentication code provided by your authenticator application.") }}
                            </p>

                            <p v-else>
                                {{ trans("Please confirm access to your account by entering one of your emergency recovery codes.") }}
                            </p>
                        </v-card-text>

                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    v-if="! recovery"
                                    v-model="form.code"
                                    ref="code" id="code" type="text" inputmode="numeric"
                                    filled
                                    :label="trans('Code')"
                                    :error-messages="form.errors.code"
                                    hide-details="auto"
                                ></v-text-field>

                                <v-text-field
                                    v-else
                                    v-model="form.recovery_code"
                                    ref="recovery_code" id="recovery_code" type="text" inputmode="numeric"
                                    filled
                                    :label="trans('Recovery code')"
                                    :error-messages="form.errors.recovery_code"
                                    hide-details="auto"
                                ></v-text-field>

                                <div class="d-flex align-center justify-space-between flex-wrap mt-5">
                                    <v-btn @click.prevent="toggleRecovery" small  color="secondary">
                                        <template v-if="! recovery">
                                            {{ trans("Use a recovery code") }}
                                        </template>

                                        <template v-else>
                                            {{ trans("Use an authentication code") }}
                                        </template>
                                    </v-btn>
                                </div>

                                <v-btn @click="submit" v-on:keyup.enter="submit" block color="primary" class="mt-6" :loading="form.processing">
                                    {{ trans("Login") }}
                                </v-btn>
                            </v-form>
                        </v-card-text>
                    </v-card>
                </div>
            </div>
        </v-layout>
    </auth-layout>
</template>

<script>
import AuthLayout from "@/Layouts/AuthLayout";

export default {
    components: {
        AuthLayout
    },

    data() {
        return {
            recovery: false,
            form: this.$inertia.form({
                code: '',
                recovery_code: '',
            })
        }
    },

    methods: {
        toggleRecovery() {
            this.recovery ^= true

            this.$nextTick(() => {
                if (this.recovery) {
                    this.$refs.recovery_code.focus()
                    this.form.code = '';
                } else {
                    this.$refs.code.focus()
                    this.form.recovery_code = ''
                }
            })
        },

        submit() {
            this.form.post(this.route('two-factor.submit'))
        }
    }
}
</script>
