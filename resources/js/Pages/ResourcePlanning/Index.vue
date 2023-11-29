<template>
    <app-layout>
        <app-head title="Resource planning" />

        <v-col class="page--header ps-0 my-5 pb-0">
            <h1>
                {{ trans('Resource planning') }}
            </h1>
        </v-col>

        <v-tabs hide-arrows v-model="activeTab" class="mb-5">
            <v-tab>
                {{ trans("Resource planning between groups") }}
            </v-tab>
            <v-tab>
                {{ trans("Resource planning between workers") }}
            </v-tab>
            <v-tab>
            {{ trans("Pre planning") }}
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
            <v-tab-item>
                <group-tab :refresh-pending="refreshGroup" @refreshed="refreshGroup = false" />
            </v-tab-item>

            <v-tab-item>
                <planning-tab :clients="clients" :all-roles="roles" :views="views" :refresh-pending="refreshWorker" @refreshed="refreshWorker = false" />
            </v-tab-item>

            <v-tab-item>
                <pre-planning-tab :all-roles="roles" :views="views" :refresh-pending="refreshPre" @refreshed="refreshPre = false" />
            </v-tab-item>
        </v-tabs-items>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import GroupTab from "@/Pages/ResourcePlanning/Tabs/GroupTab";
import PlanningTab from "@/Pages/ResourcePlanning/Tabs/PlanningTab";
import PrePlanningTab from "@/Pages/ResourcePlanning/Tabs/PrePlanningTab";

export default {
    name: "ResourcePlanningIndex",
    components: {
        AppLayout,
        GroupTab,
        PlanningTab,
        PrePlanningTab
    },

    props: ['views', 'tabs', 'tab', 'roles', 'clients'],

    data() {
        return {
            activeTab: this.tab,
            refreshGroup: false,
            refreshWorker: false,
            refreshPre: false,
        }
    },

    watch: {
        activeTab(val) {
            this.refreshGroup = false
            this.refreshWorker = false
            this.refreshPre = false

            if (val == 0) {
                this.refreshGroup = true
            }else if (val == 1) {
                this.refreshWorker = true
            }else if (val == 2) {
                this.refreshPre = true
            }

            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + this.tabs.find(x => x.text == val).value;
                window.history.pushState({path:newurl},'',newurl);
            }
        }
    }
};
</script>