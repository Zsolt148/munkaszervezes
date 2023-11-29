<template>
    <div>
        <v-row class="mt-0">
            <v-col md="12" cols="12">
                <v-row>
                    <v-col cols="12" md="4">
                        <v-text-field
                            v-model="search"
                            :placeholder="trans('Search')"
                            prepend-inner-icon="mdi-magnify"
                            id="search"
                            ref="search"
                            filled
                            clearable
                            dense
                            hide-details="auto"
                        />
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-select
                            :items="statuses"
                            v-model="selectedStatus"
                            :placeholder="trans('Status')"
                            clearable
                            hide-details="auto"
                            dense
                            filled
                        ></v-select>
                    </v-col>

                    <v-col cols="12" md="4">
                        <v-select
                            :items="roles"
                            :placeholder="trans('Roles')"
                            v-model="selectedRole"
                            clearable
                            hide-details="auto"
                            dense
                            filled
                        ></v-select>
                    </v-col>

                </v-row>
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <v-data-table
                    :headers="headers"
                    :search="search"
                    :items="filteredAdmins"
                    :no-data-text="trans('No data')"
                    :no-results-text="trans('No results')"
                    item-key="id"
                >
                    <template v-slot:item.name="{ item }">
                        <v-avatar size="40px" class="me-2">
                            <v-img :src="getProfPicture(item.photo, item.name)"></v-img>
                        </v-avatar>
                        <span class="cursor-pointer" @click="editUser(item)">
                            <b class="table--bold">{{ item.name }}</b>
                        </span>
                    </template>

                    <template v-slot:item.email="{ item }">
                        <span>{{ item.email }}</span>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <div v-if="!item.trashed">
                            <span>{{ item.statusText }}</span><br>
                            <small>{{ dateFormat(item.statusDate) }}</small>
                        </div>
                        <div v-else>
                            <span style="color:#BA1B1B!important">{{ trans('Deleted') }}</span><br>
                            <small style="color:#BA1B1B!important">{{ dateFormat(item.deleted_at) }}</small>
                        </div>
                    </template>

                    <template v-slot:item.rolesText="{ item }">
                        <span>{{ item.rolesText }}</span>
                    </template>

                    <template v-slot:item.last_login_at="{ item }">
                        <span>{{ item.last_login_at }}</span>
                    </template>

                    <template v-slot:item.actions="{ item }">
                        <v-menu
                            rounded
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list dense>
                                <v-list-item v-if="!item.trashed" @click="viewUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-eye-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Show') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item v-if="!item.trashed && item.id !== $page.props.auth.user.id" @click="impersonateUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-incognito</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Impersonate') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item @click="editUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-pencil-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Edit') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item v-if="!item.trashed" @click="deleteUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-delete-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                            {{ trans('Delete') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item v-else-if="item.trashed" @click="restoreUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-restore</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                            {{ trans('Restore') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item v-if="item.trashed" @click="forceDeleteUser(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-delete-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Force delete') }}
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
        <confirms-modal ref="confirm" />
    </div>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";

export default {
    name: "AdminsTable",
    components: {
        ConfirmsModal
    },
    props: ["admins", "roles", "statuses"],
    methods: {
        viewUser(user) {
            this.$inertia.visit(this.route("admins.show", user.id));
        },

        impersonateUser(user) {
            this.$inertia.visit(this.route("admin-proxy.enter", user.id))
        },

        editUser(user) {
            this.$inertia.visit(this.route("admins.edit", user.id));
        },

        async deleteUser(user) {
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.delete(this.route("admins.destroy", user.id));
                this.restoreOrDeleteForm.id = user.id
            }
        },
        async forceDeleteUser(user) {
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.id = user.id
                this.restoreOrDeleteForm.delete(this.route("admins.force-delete", user.id));
            }
        },
        async restoreUser(user) {
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.id = user.id
                this.restoreOrDeleteForm.patch(this.route("admins.restore", user.id));
            }
        },
    },
    data: function () {
        return {
            search: "",
            labelText: `Search...`,
            selectedRole: null,
            selectedStatus: null,
            headers: [
                { text: this.trans('Name'), value: "name" },
                { text: this.trans("Contact informations"), value: "email" },
                { text: this.trans("Roles"), value: "rolesText" },
                { text: this.trans("Status"), value: "status" },
                { text: this.trans("Last login at"), value: "last_login_at" },
                { text: this.trans("Actions"), value: 'actions', sortable: false, align: 'end'},
            ],
            restoreOrDeleteForm: this.$inertia.form({
                id: null
            }),
        };
    },

    computed: {
        filteredAdmins() {
            let admins = Array.from(this.admins)

            let role = this.selectedRole
            if (role) {
                admins = admins.filter(x => x.roleIds.includes(role))
            }

            let status = this.selectedStatus
            if (status) {
                admins = admins.filter(x => x.status == status)
            }

            return admins
        }
    }
};
</script>
