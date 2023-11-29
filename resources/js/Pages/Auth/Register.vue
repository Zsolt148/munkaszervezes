<template>
    <auth-layout>
        <AppHead :title="trans('Register')" />
        <v-layout align-center justify-center>
            <div class="auth-wrapper auth-v1">
                <div class="auth-inner">
                    <v-card elevation="4" class="auth-card">
                        <v-card-text>
                            <p class="text-h5 font-weight-semibold mb-1">
                                {{ trans('Welcome to xtracms! 👋🏻') }}
                            </p>
                            <p class="mb-2">
                                {{ trans('Create your account') }}
                            </p>
                        </v-card-text>

                        <v-card-text>
                            <v-form>
                                <v-text-field
                                    v-model="form.name"
                                    name="name"
                                    autocomplete="name"
                                    filled
                                    :label="trans('Name')"
                                    type="text"
                                    placeholder="John Doe"
                                    :error-messages="form.errors.name"
                                    hide-details="auto"
                                    class="mb-3"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.email"
                                    name="email"
                                    autocomplete="email"
                                    filled
                                    label="Email"
                                    type="email"
                                    placeholder="john@example.com"
                                    :error-messages="form.errors.email"
                                    hide-details="auto"
                                    class="mb-3"
                                ></v-text-field>

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
                                    @click:append="isPasswordVisible = !isPasswordVisible"
                                    class="mb-3"
                                ></v-text-field>

                                <v-text-field
                                    v-model="form.password_confirmation"
                                    filled
                                    :type="isPasswordVisible ? 'text' : 'password'"
                                    :label="trans('Password confirmation')"
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    autocomplete="password_confirmation"
                                    placeholder="············"
                                    :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                                    :error-messages="form.errors.password_confirmation"
                                    hide-details="auto"
                                    @click:append="isPasswordVisible = !isPasswordVisible"
                                ></v-text-field>

                                <div class="d-flex align-center justify-center flex-wrap mt-4">
                                    <Link :href="route('login')">{{ trans("Login") }}</Link>
                                </div>

                                <v-btn @click="submit" id="register--btn"  v-on:keyup.enter="submit" block color="primary" class="mt-6" :loading="form.processing">
                                    {{ trans("Register") }}
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
import AuthLayout from "@/Layouts/AuthLayout.vue";

export default {
    name: "Login",
    components: {
        AuthLayout,
    },
    data() {
        return {
            show: false,
            isPasswordVisible: false,
            form: this.$inertia.form({
                name: "",
                email: "",
                password: "",
                password_confirmation: "",
                remember: false,
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
                .post(this.route("register.submit"), {
                    onFinish: () => {
                        this.form.reset("password")
                    },
                });
        },
    },
};
</script>
