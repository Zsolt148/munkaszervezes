<template>
    <app-layout>
        <app-head title="Edit task" />

        <v-col class="page--header">
            <Link :href="route('tasks.index')">
                {{ trans("Back to") }} {{ trans('tasks') }}
            </Link>

            <h1 class="mt-2">
                {{ trans('Edit task') }}
            </h1>
        </v-col>

        <task-form
            @submit="submit"
            :form="form"
            :statuses="statuses"
            :parks="parks"
            :worksheet_types="worksheet_types"
            :task_types="task_types"
            :types="types"
            :roles="roles"
            :priorities="priorities"
        />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import TaskForm from "@/Pages/Tasks/TaskForm"

export default {
    name: "Edit",

    components: {
        AppLayout,
        TaskForm
    },

    props: ['task'],

    data() {
        return {
            roles: [],
            priorities: [],
            statuses: [],
            parks: [],
            task_types: [],
            worksheet_types: [],
            types: [],
            watchers: [],
            form: this.$inertia.form({
                id: this.task.id,
                priority: this.task.priority,
                status: this.task.status,
                park_id: this.task.park_id,
                task_type: this.task.task_type,
                type: this.task.type,
                subtasks: this.task.subtasks,
                role: this.task.role,
                role_id: this.task.role_id,
                responsible_id: this.task.responsible_id,
                name: this.task.name,
                description: this.task.description,
                travel_time: this.task.travel_time,
                estimated_hour: this.task.estimated_hour,
                deadline: this.task.deadline,
                tags: Object.assign({}, this.task.tags),
                date: this.task.date,
                class: this.task.class,
                files: this.task.media ?? [],
                isCreate: false,
            }),
        }
    },

    methods: {
        submit() {
            this.form.patch(this.route('tasks.update', this.task.id),{
                preserveState: false,
                preserveScroll: true
            })
        },

        getWatchers() {
            this.fetchWatchers();
        },


        async getPageData() {
            this.loading = true

            const resp = await axios.get(this.route('tasks.props'))

            this.parks = resp.data.parks
            this.roles = resp.data.roles
            this.priorities = resp.data.priorities
            this.statuses = resp.data.statuses
            this.task_types = resp.data.task_types
            this.worksheet_types = resp.data.worksheet_types
            this.types = resp.data.types
            this.loading = false
        },
    },

    created() {
        this.getPageData()
    },
}
</script>