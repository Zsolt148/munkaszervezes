<template>
    <auth-layout>
        <AppHead :title="trans('Verify email')" />
        <v-layout align-center justify-center>
            <div class="auth-wrapper auth-v1">
                <div class="auth-inner">
                    <v-card elevation="4" class="auth-card">
                        <v-card-text>
                            <p class="text-h5 font-weight-semibold mb-1">
                                {{ trans("Welcome to xtracms! 👋🏻") }}
                            </p>
                            <p class="mb-2">
                                {{ trans("Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
                            </p>
                        </v-card-text>

                        <v-card-text v-if="verificationLinkSent">
                            <v-alert border="left" color="success" dense outlined>
                                {{ trans("A new verification link has been sent to the email address you provided during registration.") }}
                            </v-alert>
                        </v-card-text>

                        <v-card-text>
                            <v-btn @click="submit" v-on:keyup.enter="submit" block color="primary" class="mb-5" :loading="form.processing">
                                {{ trans("Click to resend email") }}
                            </v-btn>

                            <a class="button__link" @click="logout">{{ trans('Logout') }}</a>
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
            AuthLayout,
        },

        props: {
            status: String
        },

        data() {
            return {
                form: this.$inertia.form(),
                logoutForm: this.$inertia.form({
                    method: '_POST',
                }),
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('verification.send'))
            },
            logout() {
                this.logoutForm.post(this.route('logout'))
            },
        },

        computed: {
            verificationLinkSent() {
                return this.status === 'verification-link-sent';
            }
        }
    }
</script>
