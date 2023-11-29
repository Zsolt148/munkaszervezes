<template>
    <app-layout>
        <app-head title="Create new task" />

        <v-col class="page--header">
            <a @click="$inertia.visit(route('tasks.index'))">
                {{ trans("Back to") }} {{ trans('tasks') }}
            </a>

            <h1 class="mt-2">
                {{ trans('Create new task') }}
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
import TaskForm from "@/Pages/Tasks/TaskForm";

export default {
    name: "CreateTask",

    components: {
        AppLayout,
        TaskForm
    },

    props: [''],

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
                id: "",
                priority: "medium",
                status: "todo",
                park_id: "",
                task_type: "",
                type: "",
                subtasks: [],
                role: "",
                role_id: "",
                responsible_id: "",
                name: "",
                description: "",
                travel_time: "",
                estimated_hour: "",
                deadline: "",
                tags: {},
                date: "",
                files: [],
                isCreate: true
            }),
        }
    },

    methods: {
        submit() {
            this.form.post(this.route('tasks.store'))
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