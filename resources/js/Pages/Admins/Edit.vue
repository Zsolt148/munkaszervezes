<template>
    <app-layout>
        <app-head :title="user.name" />

        <v-col>
            <Link :href="route('admins.index')">
                {{ trans("Back to users") }}
            </Link>

            <h1 class="mt-2">
                {{ trans('Edit user') }}
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
                <v-row class="mb-5">
                    <v-col cols="6" v-if="user.is_invited">
                        <v-alert prominent outlined type="info" border="left">
                            <span>{{trans("The user has not registered yet.")}}</span>
                            <v-btn
                                class="ml-5"
                                color="primary"
                                @click="reinvite"
                                :loading="reinviteForm.processing"
                            >
                                {{trans("Resend invitation")}}
                            </v-btn>
                        </v-alert>
                    </v-col>
                    <v-col cols="6" v-if="user.is_blocked">
                        <v-alert prominent outlined type="error" border="left">
                            <span>{{trans("The user has been blocked at")}}: {{ dateFormat(user.blocked_at) }}</span>
                            <v-btn
                                class="ml-5"
                                color="error"
                                @click="unblock"
                                :loading="unblockForm.processing"
                            >
                                {{trans("Unblock")}}
                            </v-btn>
                        </v-alert>
                    </v-col>
                </v-row>

                <admin-form
                    :form="form"
                    :restore-or-delete-form="restoreOrDeleteForm"
                    :roles="roles"
                    :occupationTypes="occupation_types"
                    :statuses="statuses"
                    :admins="admins"
                    @submit="submit"
                    @destroy="destroy"
                    @restore="restore"
                    @deletePhoto="deletePhoto"
                />
            </v-tab-item>
            <v-tab-item>
                <logs-table :subject="user" />
            </v-tab-item>
        </v-tabs-items>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import AdminForm from "@/Pages/Admins/AdminForm";
import LogsTable from '@/Components/Tables/LogsTable';

export default {
    name: 'Edit',
    components: {AdminForm, AppLayout, LogsTable },
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
                occupation_type: this.user.occupation_type,
                start_of_employment: this.user.start_of_employment,  
                end_of_employment: this.user.end_of_employment,  
                position: Object.assign({}, this.user.position),
                skills: Object.assign({}, this.user.skills),
            }),

            reinviteForm: this.$inertia.form({
                method: '_POST',
                user: this.user,
            }),

            unblockForm: this.$inertia.form({
                method: '_POST',
                user: this.user,
            }),

            restoreOrDeleteForm: this.$inertia.form({
                id: this.user.id
            }),
        };
    },
    methods: {
        submit() {
            this.form.put(this.route("admins.update", this.user.id),{
                preserveState: false,
                preserveScroll: true
            });
        },

        destroy() {
            this.restoreOrDeleteForm.delete(this.route("admins.destroy", this.user.id));
        },

        restore() {
            this.restoreOrDeleteForm.patch(this.route("admins.restore", this.user.id));
        },

        unblock() {
            this.unblockForm.post(this.route('admins.unblock', this.user.id))
        },

        reinvite() {
            this.reinviteForm.post(this.route('admins.reinvite', this.user.id))
        },

        deletePhoto() {
            this.form.delete(this.route("admins.deletePhoto", this.user.id), {
                onSuccess: () => {
                    this.form.profile_photo_path = null
                    this.form.photo = this.user.photo
                }
            });
        }
    },
};
</script>
