<template>
    <div>
        <v-row>
            <v-col cols="12">
                <v-alert class="create--alert" prominent>
                    <v-row align="center">
                        <v-col class="grow">
                            <p class="create--alert-text">
                                <v-icon class="me-2">
                                    mdi-checkbox-multiple-marked
                                </v-icon>

                                <b class="mx-2">{{ trans('Task manager') }}</b>

                                {{ trans('Click on the link to view all tasks.') }}
                            </p>
                        </v-col>

                        <v-col>
                            <v-btn text color="primary" style="text-decoration: underline;" class="d-block ms-auto" @click="$inertia.visit(route('tasks.index', {tab:'leaves'}))">
                                {{ trans('All tasks') }}
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-alert>
            </v-col>
        </v-row>

        <v-row>
            <v-col md="4" cols="12">
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

            <v-col class="d-flex" cols="1">
                <v-divider vertical></v-divider>

                <v-btn outlined class="ms-4"  @click="refreshTable">
                    <v-icon>
                        mdi-refresh
                    </v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <v-data-table
                    :headers="headers"
                    :search="search"
                    :items="filteredTasks"
                    :loading="loading"
                    item-key="id"
                >
                    <template v-slot:item.id="{ item }">
                        <span class="table--bold">#{{item.id}}</span>
                    </template>

                    <template v-slot:item.type="{ item }">
                        <span @click="edit(item)" class="table--field">{{ item.typeText }}</span>
                    </template>
                    
                    <template v-slot:item.task_type="{ item }">
                        <span class="table--field">{{ item.taskTypeText }}</span>
                    </template>

                    <template v-slot:item.name="{ item }">
                        <span @click="edit(item)" >
                            <b class="table--bold">{{ item.name }}</b>
                            <p v-if="item.park" class="table--small">{{ item.park.address }}</p>
                            <small v-if="item.trashed">({{ trans('Deleted') }})</small>
                        </span>
                    </template>

                    <template v-slot:item.priorty="{ item }">
                        <span>
                            <span class="table--field">{{ item.priorityText }}</span>
                        </span>
                    </template>

                    <template v-slot:item.success_at="{ item }">
                        <span>
                            <span class="table--field">{{ item.success_at }}</span>
                            <p class="table--small">12 óra</p>
                        </span>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <v-chip>{{ item.statusText }}</v-chip>
                    </template>

                    <template v-slot:item.deadline="{ item }">
                        <span>
                            <span class="table--field">{{ item.deadline }}</span>
                            <p class="table--small">{{ item.estimated_hour }} {{ trans('hour') }} </p>
                        </span>
                    </template>

                    <template v-slot:item.updated_at="{ item }">
                        <span>
                            <span class="table--field">12 órája</span>
                            <p class="table--small">{{ item.updated_at }}</p>
                        </span>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "ProfileTasksTable",
    props: {
        tasks: Array,
        loading: Boolean,
        roles: Array,
        shouldReload: {
            type: Boolean,
            required: false,
            default: false
        },
    },
    data: function () {
        return {
            search: "",
            labelText: `Search...`,
            selectedRole: null,
            selectedResponsible: null,
            menu: false,
            activePicker: null,
            responsibles: [],
            dates: [],
            headers: [
                { text: this.trans('#'), value: "id" },
                { text: this.trans('Name of task / Location'), value: "name" },
                { text: this.trans("Type"), value: "type" },
                { text: this.trans("Ticket"), value: "task_type" },
                { text: this.trans("Priority"), value: "priority" },
                { text: this.trans("Deadline / Estimated hours"), value: "deadline" },
                { text: this.trans("Status"), value: "status" },
                { text: this.trans("Updated at"), value: "updated_at" },
            ],
        };
    },

    methods: {
        edit(task){
            this.$inertia.visit(route('tasks.show', task))
        },

        refreshTable(){
            this.$emit('refreshTable');
        }
    },
    computed: {
        dateRangeText () {
            return this.dates.join(' ~ ') ?? ''
        },
        filteredTasks() {
            let tasks = Array.from(this.tasks)
            return tasks
        }
    }
};
</script>