<template>
    <section>
        <v-row>
            <v-col md="12">
                <tasks-table
                    :tasks="tasks"
                    :loading="loading"
                    @reload="fetchData"
                />
<!--                <profile-tasks-table-->
<!--                    :tasks="tasks"-->
<!--                    :loading="loading"-->
<!--                    @refreshTable="fetchData"-->
<!--                />-->
            </v-col>
        </v-row>
    </section>
</template>

<script>
import ProfileTasksTable from "@/Components/Tables/Tasks/ProfileTasksTable.vue";
import TasksTable from "@/Components/Tables/Tasks/TasksTable";

export default {
    name: 'TasksTab',
    components: {
        ProfileTasksTable,
        TasksTable
    },
    props: ['roles'],
    data() {
        return {
            loading: true,
            tasks: []
        };
    },
    created() {
        this.fetchData()
    },
    methods: {
        async fetchData() {
            this.loading = true;
            const resp = await axios.get(this.route('tasks.tab.my'))
            this.tasks = resp.data.tasks
            this.loading = false
        },
    },
};
</script>