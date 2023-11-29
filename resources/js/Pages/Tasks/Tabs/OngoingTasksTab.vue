<template>
    <div>
        <v-alert prominent color="secondary" outlined>
            <v-row align="center">
                <v-col class="grow">
                    <v-icon class="me-1">
                        mdi-pencil-box
                    </v-icon>
                    {{ trans('Click the Add new task button if you want to add a new task to the list.') }}
                </v-col>
                <v-col class="shrink d-flex">
                    <v-btn @click="$inertia.visit(route('tasks.create'))" small color="primary" class="mr-2">
                        <v-icon class="me-2">mdi-plus-circle-outline</v-icon>
                        Új feladat
                    </v-btn>
                    <v-btn @click="openTaskGenerator" small color="primary">
                        <v-icon class="me-2">mdi-plus-circle-outline</v-icon>
                        Tömeges feladat
                    </v-btn>
                </v-col>
            </v-row>
        </v-alert>

        <v-row>
            <v-col md="3" cols="12">
                <view-picker :views="views" @change="updateViewPicker"/>
            </v-col>

            <v-col cols="4" v-if="!isEmployee">
                <responsible-picker :roles="roles" @change="updateResponsiblePicker"/>
            </v-col>

            <v-col class="d-flex" cols="3">

                <v-divider vertical></v-divider>

                <v-btn :outlined="!mineTasksOnly" depressed color="primary" class="ms-4"
                       @click="mineTasksOnly = !mineTasksOnly">
                    <v-icon>mdi-check</v-icon>
                    Csak a saját feladataim
                </v-btn>

                <v-btn outlined class="ms-4" @click="fetchData">
                    <v-icon>
                        mdi-refresh
                    </v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <kan-ban :tasks="filteredTasks" :loading="loading" @click="showTask" @refresh="fetchData"/>

        <task-generator-modal
            :is-visible="isVisible"
            @close="isVisible = false"
        />
    </div>
</template>

<script>
import moment from 'moment'
import {EventBus} from '@/Components/Shared/eventbus'
import KanBan from "@/Components/Tasks/KanBan";
import ViewPicker from "@/Components/Shared/ViewPicker";
import ResponsiblePicker from "@/Components/Shared/ResponsiblePicker";
import TaskGeneratorModal from "@/Components/Shared/TaskGeneratorModal.vue";

export default {
    name: 'OngoingTasksTab',
    components: {
        ResponsiblePicker,
        ViewPicker,
        KanBan,
        TaskGeneratorModal,
    },
    props: ['priorities', 'views', 'roles', 'statuses', 'task_types', 'types'],
    data() {
        return {
            task: {},
            tasks: [],
            loading: false,
            form: this.$inertia.form({
                id: "",
                priority: "",
                status: "",
                task_type: "",
                type: "",
                role: "",
                name: "",
                description: "",
                estimated_hour: "",
                deadline: "",
                responsible: "",
                tags: {},
                date: "",
                isWatching: false,
            }),
            mineTasksOnly: false,
            showSelectedTask: false,
            controlOnStart: true,
            responsibles: [],
            working_hours: [],
            task_statuses: [],
            selectedRoles: [],
            selectedResponsibles: [],
            selectedDay: null,
            selectedWeek: null,
            selectedMonth: [
                moment().startOf('month').format('YYYY-MM-DD'),
                moment().endOf('month').format('YYYY-MM-DD')
            ],
            isVisible: false,
        }
    },

    created() {
        this.fetchData()
    },

    methods: {
        openTaskGenerator() {
            this.isVisible = true
        },

        async fetchData() {
            this.loading = true;
            const resp = await axios.get(this.route('tasks.kanban.fetch'), {
                params: {
                    day: this.selectedDay,
                    week: this.selectedWeek,
                    month: this.selectedMonth,
                }
            })
            this.tasks = resp.data.tasks
            this.loading = false
        },

        updateViewPicker({day, week, month}) {
            console.log(day, week, month)
            this.selectedDay = day
            this.selectedWeek = week
            this.selectedMonth = month
            this.fetchData()
        },

        updateResponsiblePicker({roles, responsibles}) {
            this.selectedRoles = roles
            this.selectedResponsibles = responsibles
        },

        showTask(task) {
            this.$inertia.visit(this.route("tasks.show", task.id));
        },

        async getResponsibles(task) {
            const response = await axios.get(this.route('tasks.get-responsibles', {id: task.role_id}));
            this.responsibles = response.data;
        },

        async getTaskStatuses(task) {
            const response = await axios.get(this.route('tasks.get-task-statuses', {id: task.id}));
            this.task_statuses = response.data;
        },

        async getWorkingHours(task) {
            const response = await axios.get(this.route('tasks.get-working-hours', {id: task.id}));
            this.working_hours = response.data;
        },
    },

    computed: {
        filteredTasks() {
            let tasks = Array.from(this.tasks)

            if (this.mineTasksOnly) {
                tasks = tasks.filter(x => x.responsible && x.responsible.id == this.$page.props.auth.user.id)
            }

            let roles = Array.from(this.selectedRoles)
            if (roles && roles.length) {
                tasks = tasks.filter(x => roles.includes(x.role_id))
            }

            let responsibles = Array.from(this.selectedResponsibles)
            if (responsibles && responsibles.length) {
                tasks = tasks.filter(x => responsibles.includes(x.responsible && x.responsible.id))
            }

            this.$emit('items', tasks)

            return tasks
        },
        isEmployee() {
            return this.roles.length == 1;
        }
    },

    mounted() {
        EventBus.$on('workingHoursUpdated', (newWorkingHoursData) => {
            this.working_hours = newWorkingHoursData;
        });

        EventBus.$on('isWatching', (isWatching) => {
            this.form.isWatching = isWatching;
        });
    }
};
</script>