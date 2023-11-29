<template>
    <section>
        <v-row>
            <v-col md="12">
                <tasks-table
                    :tasks="tasks"
                    :priorities="priorities"
                    :roles="roles"
                    :statuses="statuses"
                    :taskTypes="task_types"
                    :types="types"
                    :loading="loading"
                    @reload="fetchData"
                    @items="items"
                />
            </v-col>
        </v-row>
    </section>
</template>

<script>
import TasksTable from "@/Components/Tables/Tasks/DoneTasksTable.vue";

export default {
    name: 'DoneTasksTab',
    components: {
        TasksTable
    },
    props: ['roles'],
    data() {
        return {
            loading: true,
            tasks: [],
            priorities: [],
            statuses: [],
            task_types: [],
            types: [],
        };
    },
    created() {
        this.fetchData()
    },
    methods: {
        async fetchData() {
            this.loading = true;
            const resp = await axios.get(this.route('tasks.tab.done'))
            this.tasks = resp.data.tasks
            this.priorities = resp.data.priorities
            this.statuses = resp.data.statuses
            this.task_types = resp.data.task_types
            this.types = resp.data.types
            this.loading = false
        },
        items(items) {
            this.$emit('items', items)
        }
    },
};
</script>