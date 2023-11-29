<template>
    <auth-layout>
        <app-head :title="trans('Forgot Password?')" />
        <v-layout align-center justify-center>
            <v-card  outlined class="auth--card">
                
                <h1 class="auth--card-header">
                    {{ trans('Check your e-mail account!') }}
                </h1>

                <p class="auth--card-alt">
                    {{ trans('We sent you the link to the following email address:') }}
                    <br>
                    <b>{{ email }}</b>
                </p>

                <p class="auth--card-alt mb-0">
                    {{ trans('Did you not receive the email?') }}
                </p>

                <p @click="resendEmail()" class="auth--card-link">
                    {{ trans('Resend link') }}
                </p>

                <v-btn 
                    @click="$inertia.visit(route('login'))"
                    
                    block 
                    filled
                    class="mt-3"
                    color="primary">
                    {{ trans("Back to login") }}
                </v-btn>
            </v-card>
        </v-layout>
    </auth-layout>
</template>

<script>
import AuthLayout from "@/Layouts/AuthLayout.vue";

export default {
    name: 'CheckEmail',
    components: {
        AuthLayout
    },
    props: {
        email: String,
    },
    data() {
        return {
            form: this.$inertia.form({
                email: this.email,
            })
        }
    },
    methods: {
        resendEmail(){
            this.form.post(this.route('password.email'))
        }
    }
};
</script>
