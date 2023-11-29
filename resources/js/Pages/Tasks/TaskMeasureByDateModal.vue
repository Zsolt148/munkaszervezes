<template>
    <v-dialog v-model="isVisible" max-width="800px" persistent @click:outside="close">
        <v-card>
            <v-card-title class="py-0">
                <v-container>
                    <v-row class="d-flex align-center">
                        <v-col class="ps-0">
                            <v-icon class="pe-1">mdi-clock-outline</v-icon>
                            <span v-if="form.id === null">{{trans('Log time')}}</span> <span
                            v-else>{{trans('Update log time')}}</span>
                        </v-col>
                        <v-col>
                            <v-btn class="d-block ms-auto" icon light @click="close">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-title>

            <v-divider/>

            <v-card-text class="mt-5 pt-5">
                <v-row>
                    <v-col cols="12" md="7">
                        <v-row>
                            <v-col cols="12" md="12">
                                <v-select
                                    :items="tasks"
                                    :label="trans('Task')"
                                    v-model="form.task_id"
                                    @change="changeTask"
                                    item-text="name"
                                    item-value="id"
                                    clearable
                                    hide-details="auto"
                                    dense
                                    filled
                                    required
                                    prepend-icon="mdi-checkbox-multiple-marked"
                                    :error-messages="form.errors ? form.errors['task_id'] : null"
                                ></v-select>
                            </v-col>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.time"
                                    :label="trans('Duration')"
                                    :error-messages="form.errors ? form.errors['time'] : null"
                                    filled
                                    dense
                                    type="number"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-clock-time-five-outline"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-menu
                                    v-model="menu"
                                    :close-on-content-click="false"
                                    ref="menu"
                                    transition="scale-transition"
                                    offset-y
                                    max-width="290px"
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="form.date"
                                            :error-messages="form.errors ? form.errors['date'] : null"
                                            :label="trans('Date')"
                                            prepend-icon="mdi-calendar"
                                            hide-details="auto"
                                            filled
                                            dense
                                            v-bind="attrs"
                                            v-on="on">
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="form.date"
                                        no-title
                                        first-day-of-week="1"
                                        locale="hu-HU"
                                        :value="date"
                                        @input="menu = false">
                                    </v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="form.description"
                                    :label="trans('Description')"
                                    :error-messages="form.errors ? form.errors['description'] : null"
                                    filled
                                    dense
                                    type="text"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-image-text"
                                />
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="8">
                                <v-text-field
                                    v-model="remaningTime"
                                    :label="trans('Remaining estimate')"
                                    disabled
                                    filled
                                    dense
                                    type="number"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-chart-timeline-variant"
                                />
                            </v-col>

                            <v-col class="d-flex align-center" cols="4">
                                <small class="mb-0">{{trans('Original estimate')}} {{ogEstimated}}h</small>
                            </v-col>
                        </v-row>
                    </v-col>

                    <v-col cols="12" md="5">
                        <v-card v-for="wh in workingHours" outlined class="pa-2 mb-2">
                            {{wh.description}} - {{wh.time}}(h) - {{wh.date}}
                        </v-card>
                    </v-col>
                </v-row>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn v-if="form.id !== null"
                           @click="deleteLog"
                           class="ms-5"
                           color="error"
                           
                    >
                        {{trans('Delete')}}
                    </v-btn>
                    <v-btn
                        @click="submit"
                        type="submit"
                        class="ms-5"
                        color="primary"
                        
                    >
                        <span v-if="form.id === null">{{trans('Log time')}}</span> <span
                        v-else>{{trans('Update log time')}}</span>
                    </v-btn>
                </v-card-actions>
            </v-card-text>
        </v-card>
        <confirms-modal ref="confirm"/>
    </v-dialog>
</template>

<script>

import ConfirmsModal from "@/Components/Shared/ConfirmsModal";

export default {
    name: 'TaskMeasureByDateModal',
    components: {
        ConfirmsModal
    },
    props: ['date', 'form', 'isVisible', 'user', 'client'],
    data() {
        return {
            activePicker: null,
            menu: false,
            taskId: null,
            workingHours: [],
            ogEstimated: 0,
            tasks: []
        }
    },
    methods: {
        save(date) {
            this.$refs.menu.save(date)
        },
        close() {
            this.$emit("close");
        },
        fetch() {
            this.$emit("fetch");
        },
        async getTasks() {
            let adminId = this.user !== null ? this.user.id : null;
            let clientId = this.client !== null ? this.client.id : null;
            let params = {
                admin_id: adminId,
                client_id: clientId,
            }
            let response = await axios.get(this.route('tasks.get-available-data', params));
            this.tasks = response.data;
            if (this.form.task_id !== null) {
                this.changeTask();
            }
        },
        async getWorkingHours(taskId) {
            await axios.get(this.route('tasks.get-working-hours', { id: taskId }))
                .then((response) => {
                    this.workingHours = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        changeTask() {
            let selectedTask = this.tasks.find(item => item.id === this.form.task_id)
            this.ogEstimated = selectedTask.estimated_hour;
            this.getWorkingHours(this.form.task_id);
        },
        submit() {
            if (this.form.id === null) {
                if (this.form.task_id === null) {
                    this.form.errors["task_id"] = this.trans('The :attribute field is required.', {'attribute': this.trans('Task')});
                } else {
                    this.form.post(this.route('tasks.log-time', {taskId: this.form.task_id}), {
                        onSuccess: () => {
                            this.fetch();
                            this.close();
                        }
                    })
                }
            } else {
                this.form.post(this.route('tasks.update-log-time', {taskId: this.form.task_id}), {
                    onSuccess: () => {
                        this.fetch();
                        this.close();
                    }
                })
            }
        },
        async deleteLog() {
            if (await this.$refs.confirm.open()) {
                this.form.delete(this.route("tasks.task-work-log.delete", {taskWorkLogId: this.form.id}), {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.fetch();
                        this.close();
                    }
                })
            }
        }
    },
    created() {
        this.form.task_id = this.form.task_id;
        this.getTasks();
    },
    computed: {
        remaningTime() {
            let remaining = this.ogEstimated;
            this.workingHours.forEach(element => {
                remaining -= element.time;
            });
            return remaining;
        }
    }
}
</script>