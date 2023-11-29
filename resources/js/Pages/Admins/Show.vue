<template>
    <app-layout>
        <app-head :title="user.name" />

        <v-col class="page--header">
            <Link :href="route('admins.index')">
                {{ trans("Back to users") }}
            </Link>

            <h1 class="mt-2">
                {{ trans('Show user') }}
            </h1>
        </v-col>

        <v-tabs v-model="tab" class="mb-5">
            <v-tab>
                <span>{{ trans("Account") }}</span>
            </v-tab>
            <v-tab>
                <span>{{ trans("History") }}</span>
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item>
                <admin-form :form="form"
                            :disabled="true"
                            :roles="roles"
                            :occupationTypes="occupation_types"
                            :statuses="statuses"
                            :admins="admins"
                />
            </v-tab-item>
            
            <v-tab-item>
                <logs-table :subject="this.user" />
            </v-tab-item>
        </v-tabs-items>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AdminForm from "@/Pages/Admins/AdminForm";
import LogsTable from '@/Components/Tables/LogsTable';

export default {
    components: {
        AdminForm,
        AppLayout,
        LogsTable
    },
    props: [
        "user",
        "admins",
        "roles",
        "statuses",
        "occupation_types"
    ],

    data() {
        return {
            tab: null,

            form: this.$inertia.form({
                method: "_PUT",
                id: this.user.id,
                photo: this.user.photo,
                profile_photo_path: this.user.profile_photo_path,
                trashed: this.user.trashed,
                supervisor_id: this.user.supervisor_id,
                name: this.user.name,
                email: this.user.email,
                status: this.user.status,
                roles: this.user.roleIds,
                permissions: this.user.permissionIds,
                occupation_type: this.user.occupation_type,
                start_of_employment: this.user.start_of_employment,
                end_of_employment: this.user.end_of_employment,
                position: Object.assign({}, this.user.position),
                skills: Object.assign({}, this.user.skills),
            }),
        };
    },
};
</script>
