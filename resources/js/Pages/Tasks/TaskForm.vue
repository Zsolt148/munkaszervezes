<template>
    <v-form @submit.prevent="submit">
        <v-col cols="12" md="6">
            <v-row>
                <v-col cols="12" md="12">
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
                            :active-picker.sync="activePicker"
                            first-day-of-week="1"
                            locale="hu-HU"
                            min="1950-01-01"
                            @change="save"
                        ></v-date-picker>
                    </v-menu>
                </v-col>

                <v-col cols="12">
                    <v-text-field
                        v-model="form.name"
                        filled
                        dense
                        required
                        hide-details="auto"
                        :label="trans('Name of task')"
                        :error-messages="form.errors.name"
                    />
                </v-col>

                <v-col cols="12" md="12">
                    <v-select
                        :items="parks"
                        :label="trans('Park')"
                        v-model="form.park_id"
                        clearable
                        item-text="name"
                        item-value="id"
                        hide-details="auto"
                        dense
                        filled
                        :error-messages="form.errors.park_id"
                    />
                </v-col>

                <v-col cols="12" md="12">
                    <v-select
                        :items="statuses"
                        :label="trans('Task status')"
                        v-model="form.status"
                        hide-details="auto"
                        persistent-hint
                        clearable
                        dense
                        filled
                        :error-messages="form.errors.status"/>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <v-select
                        :items="task_types"
                        :label="trans('Task type')"
                        v-model="form.task_type"
                        clearable
                        hide-details="auto"
                        dense
                        filled
                        :error-messages="form.errors.task_type"/>
                </v-col>

                <v-col cols="12" v-if="showErrorDeliveryMsg">
                    <v-alert dense type="error" outlined>
                        {{ trans('Figyelem! Ennyi hibajavítás kiszállás érhető el ingyen: ') }} {{ errorDeliveryCount }} db
                    </v-alert>
                </v-col>
            </v-row>


            <sub-task-form v-if="form.task_type == 'story'" v-for="(item, index) in form.subtasks" :key="index"
                           :index="index" :form="form"/>

            <v-row v-if="form.task_type == 'story'">
                <v-col cols="12">
                    <v-btn color="primary"  @click="addSubTask">
                        <v-icon>mdi-plus</v-icon>
                        {{ trans('Add story item') }}
                    </v-btn>
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
                        :error-messages="form.errors.priority"/>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="12">
                    <v-text-field
                        v-model="form.travel_time"
                        filled
                        dense
                        type="number"
                        required
                        hide-details="auto"
                        persistent-hint
                        :placeholder="trans('hour')"
                        :label="trans('Time spent by travel (hours)')"
                        :error-messages="form.errors.travel_time"
                    />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="12">
                    <v-text-field
                        v-model="form.estimated_hour"
                        filled
                        dense
                        type="number"
                        required
                        hide-details="auto"
                        persistent-hint
                        :label="trans('Estimated hours')"
                        :error-messages="form.errors.estimated_hour"
                    />
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="12">
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
                                readonly
                                hide-details="auto"
                                filled
                                dense
                                v-bind="attrs"
                                v-on="on"/>
                        </template>
                        <v-date-picker
                            v-model="form.deadline"
                            :active-picker.sync="activePicker"
                            first-day-of-week="1"
                            locale="hu-HU"
                            min="1950-01-01"
                            @change="save"/>
                    </v-menu>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <tag-combobox
                        :model.sync="form.tags"
                        type="task_tags"
                        dense
                        filled
                    />
                </v-col>
            </v-row>

            <!-- Résztvevők -->
            <v-row>
                <v-col class="form--title" cols="12">
                    <h3>
                        Résztvevők
                    </h3>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" md="12">
                    <v-select
                        :items="roles"
                        :label="trans('Role')"
                        @change="getResponsibles"
                        v-model="form.role_id"
                        clearable
                        hide-details="auto"
                        dense
                        filled
                        :error-messages="form.errors.role"/>
                </v-col>
            </v-row>

            <v-row v-if="form.role_id">
                <v-col cols="12" md="12">
                    <v-select
                        v-model="form.responsible_id"
                        :label="trans('Responsible')"
                        :items="responsibles"
                        name="responsibles"
                        item-text="name"
                        item-value="id"
                        dense
                        hide-details="auto"
                        filled
                        :error-messages="form.errors.responsible"
                    />
                </v-col>
            </v-row>

            <!-- Feladat leírása -->
            <v-row>
                <v-col class="form--title" cols="12">
                    <h3>
                        {{ trans('Task description') }}
                    </h3>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <ck-editor :content.sync="form.description" :error="form.errors.description"/>
                </v-col>
            </v-row>

            <!-- Dokumentumok feltöltése -->
            <v-row>
                <v-col class="form--title" cols="12">
                    <h3>
                        Dokumentumok feltöltése
                    </h3>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12">
                    <file-upload @removeFile="removeFile" @uploaded="uploaded"/>
                </v-col>

                <v-col cols="12" v-if="files.length">

                    <div class="files--header">
                        <span>
                            #
                        </span>

                        <span>
                            {{ trans('Uploaded documents') }}
                        </span>
                    </div>

                    <div v-for="(item, index) in files" :key="item.name" class="files--list">
                        <span class="files--list-index">
                            {{ index + 1 }}
                        </span>

                        <div>
                            <a :href="item.original_url" target="_blank">{{ item.name }}</a>
                        </div>

                        <div class="ms-auto">
                            <v-icon color="red" @click="removeFile(index)">mdi-delete-outline</v-icon>
                        </div>
                    </div>
                </v-col>
            </v-row>

            <v-row>
                <v-col cols="12" class="d-flex justify-space-between">
                    <v-btn
                        color="secondary"
                        outlined
                        @click="$inertia.visit(route('tasks.index'))"
                        >
                        <span>{{ trans('Cancel') }}</span>
                    </v-btn>
                    <v-btn
                        color="primary"
                        type="submit"
                        :disabled="disabled"
                        :loading="form.processing">
                        <span>{{ trans('Save') }}</span>
                    </v-btn>
                </v-col>
            </v-row>
        </v-col>
    </v-form>
</template>

<script>
import TagCombobox from "@/Components/Shared/TagCombobox";
import CkEditor from "@/Components/Ckeditor/CkEditor";
import FileUpload from "@/Components/Shared/FileUpload";
import SubTaskForm from "@/Pages/Tasks/SubTaskForm";
import error from "@/Pages/Errors/Error.vue";

export default {
    name: "TaskForm",

    components: {
        SubTaskForm,
        TagCombobox,
        CkEditor,
        FileUpload
    },

    emits: ['submit'],

    props: {
        form: {
            type: Object,
            required: true,
        },
        task_types: {
            type: Array,
        },
        parks: {
            type: Array,
        },
        statuses: {
            type: Array,
            required: false,
        },
        isNew: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        roles: {
            type: Array
        },
        priorities: {
            type: Array
        }
    },

    data() {
        return {
            files: this.form.files ?? [],
            date: null,
            lang: 'hu',
            isDeleteTask: false,
            activePicker: null,
            menu: false,
            menu2: false,
            responsibles: [],
            showErrorDeliveryMsg: false,
            errorDeliveryCount: null,
        }
    },

    methods: {
        addSubTask() {
            this.form.subtasks.push(null)
        },
        uploaded(file) {
            this.files.push(file)
        },
        save(date) {
            this.$refs.menu.save(date)
        },
        async getResponsibles() {
            await axios.get(this.route('tasks.get-responsibles', {id: this.form.role_id}))
                .then((response) => {
                    this.responsibles = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        removeFile(index) {
            this.files.splice(index, 1)
        },
        submit(e) {
            if (e.submitter['data-ck-expando'] === undefined) {
                this.form.tags = Object.assign({}, this.form.tags)
                this.form.files = this.files
                this.$emit("submit")
                this.form.files = []
            }
        },
    },

    watch: {
        'form.role_id'(val, old) {
            if (val !== old) {
                this.form.responsible_id = null
            }
        },
    },

    mounted() {
        if (!this.form.isCreate) {
            this.getResponsibles()
        }
    },
}
</script>