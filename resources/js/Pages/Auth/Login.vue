<template>
    <auth-layout>
        <app-head title="Login"/>
        <v-layout align-center justify-center>
            <v-card outlined class="auth--card">
                <nav-logo />
                <h1 class="mb-5">
                    {{ trans('Login') }}
                </h1>
                <v-card-text class="pa-0">
                    <v-form>
                        <v-text-field
                            v-model="form.email"
                            id="email"
                            name="email"
                            autocomplete="email"
                            filled
                            label="Email"
                            type="email"
                            placeholder="john@example.com"
                            :error-messages="form.errors.email"
                            hide-details="auto"
                            class="mb-3"/>

                        <v-text-field
                            v-model="form.password"
                            filled
                            :type="isPasswordVisible ? 'text' : 'password'"
                            :label="trans('Password')"
                            id="password"
                            name="password"
                            autocomplete="password"
                            placeholder="············"
                            :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                            :error-messages="form.errors.password"
                            hide-details="auto"
                            @click:append="isPasswordVisible = !isPasswordVisible"/>

                        <div class="flex-wrap mt-4">
                            <Link :href="route('password.request')">{{ trans("I forgot my password") }}</Link>
                        </div>

                        <v-btn id="login--button" @click="submit" v-on:keyup.enter="submit" block color="primary"
                               class="mt-6" :loading="form.processing">
                            {{ trans('Login') }}
                        </v-btn>
                    </v-form>
                </v-card-text>
            </v-card>
        </v-layout>
    </auth-layout>
</template>

<script>
import AuthLayout from "@/Layouts/AuthLayout.vue";
import NavLogo from "@/Components/Shared/Nav/NavLogo.vue";

export default {
    name: "Login",
    components: {
        NavLogo,
        AuthLayout,
    },
    data() {
        return {
            show: false,
            isPasswordVisible: false,
            form: this.$inertia.form({
                email: "",
                password: "",
            }),
        };
    },
    methods: {
        submit() {
            this.form
                .transform((data) => ({
                    ...data,
                }))
                .post(this.route("login"), {
                    onFinish: () => {
                        this.form.reset("password")
                    },
                });
        },
    },
};
</script>
