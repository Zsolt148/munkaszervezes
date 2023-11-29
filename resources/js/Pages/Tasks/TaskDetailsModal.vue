<template>
    <v-dialog 
        v-model="isVisible" 
        max-width="1200px" 
        scrollable
        persistent 
        @click:outside="close">
        <v-card>
            <v-card-title class="pb-0 pt-1">
                <v-container>
                    <v-row>
                        <v-col>
                            {{ form.name }} - #{{ form.id }}
                        </v-col>
                        <v-col style="justify-content:end" class="ms-auto d-flex">

                            <task-watcher-menu 
                                :taskId="form.id"
                                :watchers="form.watchers"
                                :isWatching="form.isWatching"
                            />

                            <v-btn
                                class="d-inline ms-4"
                                icon
                                color="primary"
                            >
                                <v-icon>mdi-share</v-icon>
                            </v-btn>

                            <v-btn class="d-inline ms-4" icon light @click="close">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-title>

            <v-divider/>

            <v-card-text class="mt-3">
                <v-container>
                    <v-row>
                        <v-col cols="12" md="8">
                            <v-row>
                                <v-col cols="12">
                                    {{ trans('Original estimate') }}: {{ form.estimated_hour }}(h)
                                    <br>
                                    {{ trans('Logged time') }}: {{ Number(loggedTime) }}(h)
                                </v-col>

                                <v-col cols="12" v-show="taskStatuses.length">
                                    <v-card class="pa-4" outlined>
                                        <b>Státuszokban eltöltött idő</b>
                                        <div v-for="status in taskStatuses" :key="status.id">
                                            {{ status.statusText }}: {{ status.duration }}
                                        </div>
                                    </v-card>
                                </v-col>

                                <v-col cols="12">
                                    <ck-editor :content.sync="form.description" :error="form.errors.description" />
                                </v-col>

                                <v-col>
                                    <task-comments :task="task" />
                                </v-col>
                            </v-row>
                        </v-col>

                        <v-col style="
                            height: 100vh;
                            overflow:scroll;
                            position: sticky;
                            top: 0px;"
                            cols="12" md="4">
                            <v-card outlined>
                                <v-card-subtitle><b>{{ trans('Details') }}</b></v-card-subtitle>
                                <v-divider></v-divider>
                                <v-card-text>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-text-field
                                                v-model="form.name"
                                                :label="trans('Summary')"
                                                :error-messages="form.errors ? form.errors['name'] : null"
                                                filled
                                                dense
                                                required
                                                hide-details="auto"
                                            />
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-select
                                                :items="statuses"
                                                :label="trans('Status')"
                                                v-model="form.status"
                                                :error-messages="form.errors ? form.errors['status'] : null"
                                                clearable
                                                dense
                                                filled
                                                hide-details="auto"
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                
                                    <v-divider class="my-5"></v-divider>
                
                                    <v-row>
                                        <v-col cols="12" md="12">
                                            <v-select
                                                :items="types"
                                                :label="trans('Type')"
                                                :error-messages="form.errors ? form.errors['type'] : null"
                                                v-model="form.type"
                                                clearable
                                                hide-details="auto"
                                                dense
                                                filled
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                
                                    <v-row>
                                        <v-col cols="12" md="12">
                                            <v-select
                                                disabled
                                                :items="roles"
                                                :label="trans('Role')"
                                                v-model="form.role"
                                                :error-messages="form.errors ? form.errors['role'] : null"
                                                clearable
                                                hide-details="auto"
                                                dense
                                                filled
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                                    
                                    <v-row>
                                        <v-col cols="12" md="12">
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
                                                :error-messages="form.errors ? form.errors['priority'] : null"
                                                clearable
                                                hide-details="auto"
                                                dense
                                                filled
                                            ></v-select>
                                        </v-col>
                                    </v-row>
                
                                    <v-row>
                                        <v-col cols="12">
                                            <tag-combobox 
                                                :dense="true"
                                                :lang="lang"
                                                :model.sync="form.tags" 
                                                type="form_tags"
                                            />
                                        </v-col>
                                    </v-row>
                
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
                                                        v-on="on">
                                                    </v-text-field>
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
                                        <v-col cols="12" md="6">
                                            <v-text-field
                                                v-model="form.estimated_hour"
                                                filled
                                                dense
                                                type="number"
                                                required
                                                hide-details="auto"
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
                                                    @change="save"
                                                    min="1950-01-01"
                                                    first-day-of-week="1"
                                                    locale="hu-HU"
                                                ></v-date-picker>
                                            </v-menu>
                                        </v-col>
                                    </v-row>

                                    <v-row>
                                        <v-col cols="12">
                                            <v-btn
                                                @click="showMeasureModal"
                                                color="primary"
                                                dark
                                                small
                                                
                                            >
                                                {{ trans('Log time') }}
                                            </v-btn>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-container>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="submit"
                        :loading="form.processing"
                        type="submit"
                        class="ms-5"
                        color="primary"
                        
                    >
                        {{ trans('Save') }}
                    </v-btn>
                </v-card-actions>
            </v-card-text>
        </v-card>

        <task-measure-modal 
            :isVisible="measureModal"
            :workingHours="workingHours"
            :taskId="form.id"
            :ogEstimated="form.estimated_hour"
            @close="closeMeasureModal"
        />
    </v-dialog>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal.vue"
import TagCombobox from "@/Components/Shared/TagCombobox";
import TaskMeasureModal from "./TaskMeasureModal.vue";
import TaskWatcherMenu from "./TaskWatcherMenu.vue";
import TaskComments from "./TaskComments.vue";
import CkEditor from "@/Components/Ckeditor/CkEditor";

export default {
    name: "TaskMeaureModal",
    emits: ['update', 'close'],

    components:{
        ConfirmsModal,
        TagCombobox,
        TaskMeasureModal,
        CkEditor,
        TaskWatcherMenu,
        TaskComments
    },

    props:{
        form: Object,
        isVisible: Boolean,
        statuses: Array,
        taskTypes: Array,
        types: Array,
        roles: Array,
        priorities: Array,
        responsibles: Array,
        taskStatuses: Array,
        workingHours: Array,
        task: Object,
    },

    data() {
        return {
            lang: 'hu',
            activePicker: null,
            activePicker2: null,
            menu: false,
            menu2: false,
            measureModal: false,
            showMenu: false,
        };
    },

    methods: {
        showMeasureModal(){
            this.measureModal = true;
        },

        closeMeasureModal(){
            this.measureModal = false;
        },

        save (date) {
            this.$refs.menu.save(date)
        },

        close() {
            this.$emit("close");
        },

        submit() {
            this.form.tags = Object.assign({}, this.form.tags)
            this.$emit("update", this.form);
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
    computed:{
        loggedTime(){
            let counter = 0;
            this.workingHours.forEach(element => {
                counter += Number(element.time);
            });
            return counter;
        },
    }
};
</script>

<style scoped>
.tox-tinymce{
    border: 1px solid #666666;
}

</style>