<template>
    <app-layout>
        <app-head title="Tempo" />

        <tempo :user="$page.props.auth.user" />
    </app-layout>
</template>

<script>
import Tempo from "@/Components/Shared/Tempo";
import AppLayout from "@/Layouts/AppLayout.vue";

export default {
    name: 'ProfileTempo',
    components: {
        AppLayout,
        Tempo
    },
    data(){
        return{
            tab: null,
        }
    },
}
</script>