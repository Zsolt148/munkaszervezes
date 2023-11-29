<template>
    <app-layout>
        <app-head title="Notifications" />
        <notifications-table :notifiable="user" />
    </app-layout>
</template>
<script>

import AppLayout from "@/Layouts/AppLayout";
import NotificationsTable from "@/Components/Tables/NotificationsTable";

export default {
    name: "Index",
    props: ['user'],
    components: {
        AppLayout,
        NotificationsTable
    },
    data(){
        return{
            search: '',
        }
    },
}
</script>
