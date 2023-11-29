<template>
    <v-form class="">
        <v-row>
            <v-col cols="12" md="6" class="pa-0">
                <v-col cols="12">
                    <v-text-field
                        v-model="form.name"
                        :disabled="disabled"
                        :error-messages="form.errors.name"
                        :label="trans('Name')"
                        hide-details="auto"
                        name="name"
                        dense
                        filled/>
                </v-col>

                <v-col cols="12">
                    <v-text-field
                        v-model="form.email"
                        :disabled="disabled"
                        :error-messages="form.errors.email"
                        hide-details="auto"
                        placeholder="john@example.com"
                        label="E-mail"
                        type="email"
                        name="email"
                        dense
                        filled/>
                </v-col>

                <v-col cols="12">
                    <v-text-field
                        v-if="disabled"
                        :disabled="disabled"
                        v-model="form.roles"
                        :label="trans('Roles')"
                        hide-details="auto"
                        filled
                        dense/>

                    <v-autocomplete
                        v-else
                        :items="roles"
                        :disabled="disabled"
                        :error-messages="form.errors.roles"
                        :label="trans('Roles')"
                        v-model="form.roles"
                        hide-details="auto"
                        name="roles"
                        multiple
                        dense
                        filled/>
                </v-col>

                <v-col v-if="roles && !form.roles.includes(2)" cols="12">
                    <v-autocomplete
                        v-model="form.supervisor_id"
                        :label="trans('Supervisor')"
                        :errors-messages="form.errors.supervisor_id"
                        :items="admins"
                        :disabled="disabled"
                        hide-details="auto"
                        name="supervisor"
                        item-text="name"
                        item-value="id"
                        dense
                        filled/>
                </v-col>

                <v-col cols="12">
                    <v-select
                        v-model="form.occupation_type"
                        :disabled="disabled"
                        :label="trans('Type of occupation')"
                        :error-messages="form.errors ? form.errors['occupation_type'] : null"
                        :items="occupationTypes"
                        name="occupation_types"
                        hide-details="auto"
                        dense
                        filled/>
                </v-col>

                <v-col cols="12">
                    <v-menu
                        ref="startOfEmploymentMenu"
                        v-model="startOfEmploymentMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="form.start_of_employment"
                                :label="trans('Start of employment')"
                                :error-messages="form.errors.start_of_employment"
                                :disabled="disabled"
                                hide-details="auto"
                                filled
                                readonly
                                dense
                                v-bind="attrs"
                                v-on="on"/>
                        </template>
                        <v-date-picker
                            v-model="form.start_of_employment"
                            :active-picker.sync="activePicker"
                            first-day-of-week="1"
                            locale="hu-HU"
                            @change="saveStartOfEmployment"
                        ></v-date-picker>
                    </v-menu>
                </v-col>

                <v-col v-if="!isNew" cols="12">
                    <v-menu
                        ref="endOfEmploymentMenu"
                        v-model="endOfEmploymentMenu"
                        :close-on-content-click="false"
                        transition="scale-transition"
                        offset-y
                        min-width="auto"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-text-field
                                v-model="form.end_of_employment"
                                :label="trans('End of employment')"
                                :error-messages="form.errors.end_of_employment"
                                :disabled="disabled"
                                filled
                                readonly
                                dense
                                v-bind="attrs"
                                v-on="on"/>
                        </template>
                        <v-date-picker
                            v-model="form.end_of_employment"
                            :active-picker.sync="activePicker"
                            first-day-of-week="1"
                            locale="hu-HU"
                            @change="saveEndOfEmployment"/>
                    </v-menu>
                </v-col>

                <v-col class="mt-3" v-if="isNew">
                    <small class="form--alt">{{
                            trans('We send a registration link to the invited user by email.')
                        }}</small>
                </v-col>

                <v-col cols="12" class="d-flex justify-space-between">
                    <v-btn
                        v-if="disabled"
                        color="primary"
                        @click="$inertia.visit(route('admins.edit', form.id))">
                        {{ trans('Edit') }}
                    </v-btn>

                    <v-btn
                        v-else
                        color="primary"
                        @click="$emit('submit')"
                        :disabled="disabled"
                        :loading="form.processing">
                        <span v-if="isNew">{{ trans('Invite new user') }}</span>
                        <span v-else>{{ trans('Save changes') }}</span>
                    </v-btn>

                    <span v-if="restoreOrDeleteForm">
                        <confirms-modal v-if="!form.trashed" @confirmed="$emit('destroy')">
                            <v-btn
                                color="error"
                                :disabled="disabled"
                                :loading="restoreOrDeleteForm.processing">
                                {{ trans('Delete user') }}
                            </v-btn>
                        </confirms-modal>
                        <confirms-modal v-else @confirmed="$emit('restore')">
                            <v-btn
                                color="error"
                                filled
                                :disabled="disabled"
                                :loading="restoreOrDeleteForm.processing">
                                {{ trans('Restore') }}
                            </v-btn>
                        </confirms-modal>
                    </span>
                </v-col>
            </v-col>
        </v-row>
    </v-form>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";
import TagCombobox from "@/Components/Shared/TagCombobox";

export default {
    name: "AdminForm",

    components: {
        ConfirmsModal,
        TagCombobox
    },

    data() {
        return {
            activePicker: null,
            date: null,
            startOfEmploymentMenu: false,
            endOfEmploymentMenu: false,
        }
    },

    methods: {
        saveStartOfEmployment(date) {
            this.$refs.startOfEmploymentMenu.save(date)
        },

        saveEndOfEmployment(date) {
            this.$refs.endOfEmploymentMenu.save(date)
        },
    },

    props: {
        form: {
            type: Object,
            required: true,
        },
        restoreOrDeleteForm: {
            type: Object,
            required: false,
        },
        statuses: {
            type: Array,
            required: false,
        },
        roles: {
            type: Array,
            required: false,
        },
        admins: {
            type: Array,
            required: false
        },
        occupationTypes: {
            type: Array,
        },
        isNew: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        }
    },

    emits: ['submit', 'destroy', 'restore'],
}
</script>