<template>
    <error-layout>
        <v-container class="text-center">
            <h1>{{ title }}</h1>
            <div>{{ description }}</div>
            <v-btn @click="$inertia.visit(route('dashboard'))" v-if="status == 404"  text
                   class="mt-4"
                   color="primary">
                {{ trans('Go back') }}
            </v-btn>
        </v-container>
    </error-layout>
</template>

<script>
import ErrorLayout from "@/Layouts/ErrorLayout.vue";

export default {
    components: {ErrorLayout},
    props: {
        status: Number,
    },
    computed: {
        title() {
            return {
                503: this.trans("503: Service Unavailable"),
                500: this.trans("500: Server Error"),
                404: this.trans("404: Page Not Found"),
                403: this.trans("403: Forbidden"),
            }[this.status];
        },
        description() {
            return {
                503: this.trans("Sorry, we are doing some maintenance. Please check back soon."),
                500: this.trans("Whoops, something went wrong on our servers."),
                404: this.trans("Sorry, the page you are looking for could not be found."),
                403: this.trans("Sorry, you are forbidden from accessing this page."),
            }[this.status];
        },
    },
};
</script>
