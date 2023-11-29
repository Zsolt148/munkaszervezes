<template>
    <app-layout>
        <app-head title="Tasks"/>

        <tempo/>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import Tempo from "@/Components/Shared/Tempo.vue";

export default {
    name: "TaskTempo",
    props: [],
    components: {
        AppLayout,
        Tempo
    },
};
</script>