<template>
    <v-dialog v-model="isVisible" max-width="800px" persistent @click:outside="close">
        <v-card>
            <v-card-title>
                <v-container>
                    <v-row>
                        <v-col>
                            <span>{{form.isCreate ? trans('Create new task') : trans('Edit task')}}</span>
                        </v-col>
                        <v-col>
                            <v-btn class="d-block ms-auto" icon light @click="close">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12" md="6">
                            <v-select
                                :items="statuses"
                                :label="trans('Status')"
                                v-model="form.status"
                                :hint="trans('This is the issues initial status upon creation')"
                                persistent-hint
                                clearable
                                dense
                                filled
                                :error-messages="form.errors.status"
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-divider class="my-5"></v-divider>

                    <v-row>
                        <v-col cols="12" md="12">
                            <v-select
                                :items="types"
                                :label="trans('Type')"
                                v-model="form.type"
                                clearable
                                hide-details="auto"
                                dense
                                filled
                                :error-messages="form.errors ? form.errors['type'] : null"
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="6">
                            <v-select
                                :items="roles"
                                :label="trans('Role')"
                                @change="getResponsibles"
                                v-model="form.role"
                                clearable
                                hide-details="auto"
                                dense
                                filled
                                :error-messages="form.errors ? form.errors['role'] : null"
                            ></v-select>
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-select
                                v-if="form.role"
                                v-model="form.responsible"
                                :label="trans('Responsible')"
                                :errors-messages="form.errors.responsible"
                                :items="responsibles"
                                name="responsibles"
                                item-text="name"
                                item-value="id"
                                dense
                                hide-details="auto"
                                filled
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="12">
                            <v-select
                                :items="priorities"
                                :label="trans('Priority')"
                                v-model="form.priority"
                                clearable
                                hide-details="auto"
                                dense
                                filled
                                :error-messages="form.errors ? form.errors['priority'] : null"
                            ></v-select>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <tag-combobox
                                :dense="true"
                                :lang="lang"
                                :model.sync="form.tags"
                                type="task_tags"
                            />
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="6">
                            <v-menu
                                ref="menu2"
                                v-model="menu2"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.date"
                                        :label="trans('Date')"
                                        :error-messages="form.errors.date"
                                        filled
                                        readonly
                                        hide-details="auto"
                                        dense
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.date"
                                    :active-picker.sync="activePicker2"
                                    min="1950-01-01"
                                    first-day-of-week="1"
                                    locale="hu-HU"
                                    @change="save"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.name"
                                filled
                                dense
                                required
                                hide-details="auto"
                                :label="trans('Name')"
                                :error-messages="form.errors ? form.errors['name'] : null"
                            />
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12">
                            <ck-editor :content.sync="form.description" :error="form.errors.description" />
                        </v-col>
                    </v-row>

                    <v-row>
                        <v-col cols="12" md="6">
                            <v-text-field
                                v-model="form.estimated_hour"
                                filled
                                dense
                                type="number"
                                required
                                hide-details="auto"
                                hint="Becslések szerint mennyi munka van hátra a probléma megoldásáig."
                                persistent-hint
                                :label="trans('Estimated hours')"
                                :error-messages="form.errors ? form.errors['estimated_hour'] : null"
                            />
                        </v-col>

                        <v-col cols="12" md="6">
                            <v-menu
                                ref="menu"
                                v-model="menu"
                                :close-on-content-click="false"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="form.deadline"
                                        :label="trans('Deadline')"
                                        :error-messages="form.errors.deadline"
                                        filled
                                        readonly
                                        hide-details="auto"
                                        dense
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="form.deadline"
                                    :active-picker.sync="activePicker"
                                    min="1950-01-01"
                                    first-day-of-week="1"
                                    locale="hu-HU"
                                    @change="save"
                                ></v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                </v-container>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        v-if="!form.trashed"
                        @click="submit"
                        :loading="form.processing"
                        type="submit"
                        class="ms-5"
                        color="primary"
                        
                    >
                        {{trans('Save task')}}
                    </v-btn>

                    <span v-if="restoreOrDeleteForm">
                        <confirms-modal v-if="!form.trashed && !form.isCreate" @confirmed="destroy()">
                            <v-btn
                                color="error"
                                filled
                                
                                class="ms-2"
                                :loading="restoreOrDeleteForm.processing"
                            >
                                {{trans('Delete')}}
                            </v-btn>
                        </confirms-modal>
                        <confirms-modal v-if="form.trashed" @confirmed="forceDelete()">
                            <v-btn
                                color="error"
                                filled
                                
                                class="ms-2"
                                :loading="restoreOrDeleteForm.processing"
                            >
                                {{trans('Force delete')}}
                            </v-btn>
                        </confirms-modal>
                        <confirms-modal v-if="form.trashed" @confirmed="restore()">
                            <v-btn
                                color="error"
                                filled
                                
                                class="ms-2"
                                :loading="restoreOrDeleteForm.processing"
                            >
                                {{trans('Restore')}}
                            </v-btn>
                        </confirms-modal>
                    </span>
                </v-card-actions>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal.vue"
import TagCombobox from "@/Components/Shared/TagCombobox";
import CkEditor from "@/Components/Ckeditor/CkEditor";

export default {
    name: "TaskModal",
    emits: ['create', 'update', 'destroy', 'restore'],
    components: {
        ConfirmsModal,
        TagCombobox,
        CkEditor
    },
    props: {
        form: Object,
        isVisible: Boolean,
        statuses: Array,
        taskTypes: Array,
        types: Array,
        roles: Array,
        priorities: Array,
        restoreOrDeleteForm: {
            type: Object,
            required: false,
        },
    },
    data() {
        return {
            lang: 'hu',
            isDeleteTask: false,
            activePicker: null,
            activePicker2: null,
            menu: false,
            menu2: false,
            responsibles: []
        };
    },
    methods: {
        async getResponsibles() {
            await axios.get(this.route('tasks.get-responsibles', { id: this.form.role }))
                .then((response) => {
                    this.responsibles = response.data
                })
                .catch((error) => {
                    console.log(error);
                })
        },
        save(date) {
            this.$refs.menu.save(date)
        },
        close() {
            this.$emit("close");
        },
        submit() {
            this.form.tags = Object.assign({}, this.form.tags)
            if (this.form.isCreate) {
                this.$emit("create", this.form);
            } else {
                this.$emit("update", this.form);
            }
        },
        forceDelete() {
            this.$emit('forceDelete', this.form)
        },
        destroy() {
            this.$emit('destroy', this.form)
        },
        restore() {
            this.$emit('restore', this.form)
        }
    },
    mounted() {
        if (!this.form.isCreate) {
            this.getResponsibles(this.form.role)
        }
    },
    watch: {
        'form.isCreate': function (isCreate) {
            if (isCreate === false) {
                this.getResponsibles(this.form.role);
            }
        },
    }
};
</script>