<template>
    <app-layout>
        <app-head title="Create User" />

        <v-col class="page--header">
            <Link :href="route('admins.index')">
                {{ trans("Back to users") }}
            </Link>

            <h1 class="mt-2">
                {{ trans('Create User') }}
            </h1>
        </v-col>

        <admin-form 
            :form="form" 
            :admins="admins" 
            :roles="roles"
            :occupationTypes="occupation_types"
            :is-new="true" 
            @submit="submit" />

    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AdminForm from "@/Pages/Admins/AdminForm";

export default {
    name: "Index",
    components: {
        AdminForm,
        AppLayout,
    },
    props: [
        'roles', 
        'admins',
        "occupation_types"
    ],
    data() {
        return {
            form: this.$inertia.form({
                supervisor_id: "",
                name: "",
                email: "",
                roles: "",
                occupation_type: "",
                start_of_employment: "",
                position: {},
                skills: {}
            }),
        };
    },

    methods: {
        submit() {
            this.form.roles.includes(2) ? this.form.supervisor_id = "" : false;

            this.form.post(this.route("admins.store"));
        },
    },
};
</script>
