<template>
    <auth-layout>
        <app-head :title="trans('Reset password')" />
        <v-layout align-center justify-center>
            <v-card outlined  class="auth--card">

                <h1 class="auth--card-header">
                    {{ isRegister ? trans('Create your account') : trans('Set a new password') }}
                </h1>

                <v-card-text class="pa-0">
                    <v-form>
                        <v-text-field
                            :error-messages="form.errors.email"
                            v-model="form.email"
                            disabled
                            name="email"
                            autocomplete="email"
                            filled
                            label="Email"
                            type="email"
                            placeholder="john@example.com"
                            hide-details="auto"
                            class="mb-3" />

                        <v-text-field
                            v-model="form.password"
                            :type="isPasswordVisible ? 'text' : 'password'"
                            :label="trans('Password')"
                            :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                            :error-messages="form.errors.password"
                            @click:append="isPasswordVisible = !isPasswordVisible"
                            filled
                            id="password"
                            name="password"
                            autocomplete="password"
                            placeholder="············"
                            hide-details="auto"
                            class="mb-3" />

                        <v-text-field
                            v-model="form.password_confirmation"
                            :type="isPasswordVisible ? 'text' : 'password'"
                            :label="trans('Password confirmation')"
                            :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                            :error-messages="form.errors.password_confirmation"
                            filled
                            id="password"
                            name="password"
                            autocomplete="password"
                            placeholder="············"
                            hide-details="auto"
                            @click:append="isPasswordVisible = !isPasswordVisible" />

                        <v-checkbox
                            v-if="isRegister"
                            :label="trans('Remember Me')"
                            hide-details
                            class="me-3 mt-1"
                            v-model="form.remember" />

                        <v-btn  @click="submit" v-on:keyup.enter="submit" block color="primary" class="mt-6" :loading="form.processing">
                            {{ isRegister ? trans('Creating a profile and logging in') : trans('Reset password') }}
                        </v-btn>

                        <p class="auth--card-small">{{trans('After you click on the button, we will automatically log you in.')}}</p>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-layout>
    </auth-layout>
</template>

<script>
import AuthLayout from "@/Layouts/AuthLayout.vue";

export default {
    name: "ResetPassword",

    components: {
        AuthLayout,
    },

    props: ['token', 'email', 'name', 'isRegister'],

    data() {
        return {
            show: false,
            isPasswordVisible: false,
            form: this.$inertia.form({
                token: this.token,
                email: this.email,
                name: this.name,
                password: "",
                password_confirmation: "",
                remember: false,
                is_register: this.isRegister
            }),
        };
    },
    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                    remember: this.form.remember ? "on" : "",
                }))
                .post(this.route("password.update"), {
                    onFinish: () => {
                        this.form.reset("password")
                    },
                });
        },
    },
};
</script>
