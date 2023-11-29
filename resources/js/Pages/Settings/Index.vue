<template>
    <app-layout>
        <app-head title="Settings"/>

        <v-tabs v-model="tab">
            <v-tab>{{ trans('Security') }}</v-tab>

            <v-tab>
                <v-icon v-if="$page.props.page.maintenance" class="me-2">mdi-alert</v-icon>
                {{ trans('Maintenance mode') }}
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="tab">
            <v-tab-item>
                <security-form/>
            </v-tab-item>

            <v-tab-item>
                <maintenance-form/>
            </v-tab-item>
        </v-tabs-items>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout.vue";
import MaintenanceForm from "./MaintenanceForm.vue";
import SecurityForm from "@/Pages/Settings/SecurityForm";

export default {
    name: "Settings",
    props: ['settings'],
    components: {
        SecurityForm,
        AppLayout,
        MaintenanceForm
    },
    provide() {
        return {
            settings: this.settings,
        }
    },
    data() {
        return {
            tab: null,
        }
    },
}
</script>