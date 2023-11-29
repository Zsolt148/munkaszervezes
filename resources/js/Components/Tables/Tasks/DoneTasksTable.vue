<template>
    <div>
        <v-row>
            <v-col md="3" cols="12">
                <v-text-field
                    v-model="search"
                    :placeholder="trans('Search')"
                    id="search"
                    ref="search"
                    filled
                    clearable
                    dense
                    hide-details="auto"
                />
            </v-col>

            <v-col class="d-flex" cols="2">
                <v-divider vertical></v-divider>

                <v-btn outlined class="ms-4" @click="refreshTable">
                    <v-icon>
                        mdi-refresh
                    </v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="3">
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
                            v-model="dateRangeText"
                            :placeholder="trans('Date')"
                            :disabled="false"
                            append-icon="mdi-calendar"
                            readonly
                            v-bind="attrs"
                            v-on="on"
                            clearable
                            hide-details="auto"
                            dense
                            filled/>
                    </template>
                    <v-date-picker
                        v-model="dates"
                        :active-picker.sync="activePicker"
                        :format="'YYYY-MM'"
                        range
                        first-day-of-week="1"
                        locale="hu-HU"
                    ></v-date-picker>
                </v-menu>
            </v-col>

            <v-col md="3" cols="12">
                <v-select
                    :items="roles"
                    v-model="selectedRole"
                    @change="getResponsibles"
                    :placeholder="trans('Role')"
                    clearable
                    hide-details="auto"
                    dense
                    filled/>
            </v-col>

            <v-col md="2" cols="12">
                <v-select
                    :items="responsibles"
                    v-model="selectedResponsible"
                    :placeholder="trans('Responsible')"
                    name="responsibles"
                    item-text="name"
                    item-value="id"
                    clearable
                    hide-details="auto"
                    dense
                    filled/>
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <v-data-table
                    :headers="headers"
                    :search="search"
                    :items="filteredTasks"
                    :loading="loading"
                    show-expand
                    single-expand
                    item-key="id"
                >
                    <template v-slot:item.id="{ item }">
                        <span class="table--bold">#{{ item.id }}</span>
                    </template>

                    <template v-slot:item.name="{ item }">
                        <span @click="edit(item)" class="cursor-pointer">
                            <b class="table--bold">{{ item.name }}</b>
                            <small v-if="item.trashed">({{ trans('Deleted') }})</small>
                        </span>
                    </template>

                    <template v-slot:item.task_type="{ item }">
                        <span>{{ item.taskTypeText }}</span>
                    </template>

                    <template v-slot:item.priority="{ item }">
                        <v-chip>{{ item.priorityText }}</v-chip>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip>{{ item.statusText }}</v-chip>
                    </template>

                    <template v-slot:item.responsible="{ item }">
                        <span>
                            <span>{{ item.responsible ? item.responsible.name : '-' }}</span>
                            <p class="table--small">{{ item.roleText }}</p>
                        </span>
                    </template>

                    <template v-slot:item.done_at="{ item }">
                        <span>
                            <span>{{ item.done_at }}</span>
                            <p class="table--small">{{ item.working_hours_sum ? item.working_hours_sum : '-' }} {{ trans('hour') }}</p>
                        </span>
                    </template>

                    <template v-slot:item.updated_at="{ item }">
                        <span>
                            <span>~{{ item.updated_at_diff }}</span>
                            <p class="table--small">{{ dateFormat(item.updated_at) }}</p>
                        </span>
                    </template>

                    <template v-slot:item.data-table-expand="{ item, expand, isExpanded }">
                        <span v-if="item.subtasks.length">
                            <v-icon v-if="!isExpanded" @click="expand(true)">mdi-chevron-down</v-icon>
                            <v-icon v-else @click="expand(false)">mdi-chevron-up</v-icon>
                        </span>
                    </template>

                    <template v-slot:expanded-item="{ headers, item }">
                        <td :colspan="headers.length" class="p-0">
                            <v-simple-table elevation="0">
                                <template v-slot:default>
                                    <tbody>
                                    <tr v-for="task in item.subtasks" :key="task.id">
                                        <td></td>
                                        <td>
                                            <span>#{{ task.id }}</span>
                                        </td>
                                        <td>
                                            <span @click="edit(task)">
                                                <b class="table--bold">{{ task.name }}</b>
                                                <p class="table--small">{{ task.park ? task.park.name : '-' }}</p>
                                                <small v-if="task.trashed">({{ trans('Deleted') }})</small>
                                            </span>
                                        </td>
                                        <td>
                                            <span>{{ task.taskTypeText }}</span>
                                        </td>
                                        <td>
                                            <span><v-chip>{{ task.priorityText }}</v-chip></span>
                                        </td>
                                        <td>
                                            <span><v-chip>{{ task.statusText }}</v-chip></span>
                                        </td>
                                        <td>
                                            <span>
                                                <span>{{ task.responsible ? task.responsible.name : '-' }} asd</span>
                                                <p class="table--small">{{ task.roleText }}</p>
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                <span>{{ task.deadline }}</span>
                                                <p class="table--small">{{ task.estimated_hour }} {{
                                                        trans('hour')
                                                    }}</p>
                                            </span>
                                        </td>
                                        <td>
                                            <span>
                                                <span>~{{ task.updated_at_diff }}</span>
                                                <p class="table--small">{{ dateFormat(task.updated_at) }}</p>
                                            </span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </template>
                            </v-simple-table>
                        </td>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </div>
</template>

<script>
import moment from 'moment';

export default {
    name: "TasksTable",
    props: {
        tasks: Array,
        statuses: Array,
        loading: Boolean,
        roles: Array,
        priorities: Array,
        shouldReload: {
            type: Boolean,
            required: false,
            default: false
        },
    },
    data: function () {
        return {
            expanded: [],
            search: "",
            labelText: `Search...`,
            selectedRole: null,
            selectedResponsible: null,
            selectedPriority: null,
            exportItems: [],
            selectedPark: null,
            menu: false,
            activePicker: null,
            responsibles: [],
            dates: [],
            headers: [
                {text: this.trans('#'), value: "id"},
                {text: this.trans('Name of task / Park'), value: "name"},
                {text: this.trans("Ticket"), value: "task_type"},
                {text: this.trans("Priority"), value: "priority"},
                {text: this.trans("Status"), value: "status"},
                {text: this.trans("Responsible / Group"), value: "responsible"},
                {text: this.trans("Completion / Time Spent"), value: "done_at"},
                {text: this.trans("Updated at"), value: "updated_at"},
                {text: '', value: 'data-table-expand'},
            ],
        };
    },

    methods: {
        async getResponsibles() {
            await axios.get(this.route('tasks.get-responsibles', {id: this.selectedRole}))
                .then((response) => {
                    this.responsibles = response.data;
                })
                .catch((error) => {
                    console.log(error);
                });
        },

        edit(task) {
            this.$inertia.visit(this.route('tasks.show', task))
        },

        refreshTable() {
            this.$emit('reload');
        },
        items(items) {
            if (items.length) {
                const ids = items.map(i => i.id)
                const subtasks = items.flatMap(i => i.subtasks.map(s => s.id))
                this.exportItems = ids.concat(subtasks)
            } else {
                this.exportItems = []
            }
        },
    },
    computed: {
        dateRangeText: {
            get() {
                if (!this.dates.length) {
                    return ''
                }

                return this.dates.join(' ~ ') ?? ''
            },
            set() {
                this.dates = []
                return []
            }
        },

        filteredTasks() {
            let tasks = Array.from(this.tasks)

            let role = this.selectedRole
            if (role) {
                tasks = tasks.filter(x => x.role_id == role)
            }

            let dates = this.dates
            if (dates.length > 1) {
                tasks = tasks.filter(x => moment(x.done_at).isBetween(dates[0], dates[1]))
            }

            let priority = this.selectedPriority
            if (priority) {
                tasks = tasks.filter(x => x.priority == priority)
            }

            let responsible = this.selectedResponsible
            if (responsible) {
                tasks = tasks.filter(x => x.responsible.id == responsible)
            }

            this.items(tasks)
            this.$emit('items', tasks)

            return tasks
        }
    }
};
</script>
