<template>
    <app-layout>
        <app-head title="Logs" />

        <logs-table />
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import LogsTable from "@/Components/Tables/LogsTable";

export default {
    name: "Index",
    components: {
        AppLayout,
        LogsTable
    },
    props: ['logs'],
    data(){
        return{
            tab: null,
        }
    },
}
</script>