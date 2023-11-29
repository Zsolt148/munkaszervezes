<template>
    <app-layout>
        <app-head title="Tasks" />

        <v-row class="my-5">
            <v-col cols="12" class="d-flex justify-space-between">
                <h1>
                    {{ trans('Task manager') }}
                </h1>
                <div>
                    <v-btn
                        @click="exportTable"
                        filled
                        class="ms-md-2"
                        color="primary">
                        <v-icon class="me-2">mdi-export</v-icon> {{trans("Export to xlsx")}}
                    </v-btn>
                </div>
            </v-col>
        </v-row>

        <v-tabs hide-arrows v-model="activeTab" class="mb-5">
            <v-tab>
                {{ trans("Tasks") }}
            </v-tab>
            <v-tab>
                {{ trans("Done tasks") }}
            </v-tab>
            <v-tab>
                {{ trans("Holiday and sick pay") }}
            </v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
            <v-tab-item>
                <ongoing-tasks-tab
                    :priorities="priorities"
                    :roles="roles"
                    :statuses="statuses"
                    :task_types="task_types"
                    :types="types"
                    :views="views"
                    @items="items"
                />
            </v-tab-item>

            <v-tab-item>
                <done-tasks-tab :roles="roles" @items="items" />
            </v-tab-item>

            <v-tab-item>
                <leaves-tab @items="items" />
            </v-tab-item>
        </v-tabs-items>

    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import OngoingTasksTab from "@/Pages/Tasks/Tabs/OngoingTasksTab.vue";
import DoneTasksTab from "@/Pages/Tasks/Tabs/DoneTasksTab.vue";
import LeavesTab from "@/Pages/Tasks/Tabs/LeavesTab.vue";

export default {
    name: "TasksIndex",
    props: ['tab', 'tabs', 'priorities', 'views', 'roles', 'statuses', 'task_types', 'types'],
    order: 4,
    components: {
        AppLayout,
        OngoingTasksTab,
        DoneTasksTab,
        LeavesTab,
    },
    data() {
        return {
            activeTab: this.tab,
            exportItems: [],
        }
    },

    methods: {
        items(items) {
            if (items.length) {
                this.exportItems = items.map(i => i.id)
            }else {
                this.exportItems = []
            }
        },
        exportTable() {
            window.open(this.route('tasks.export', {
                tab: this.activeTab,
                ids: this.exportItems
            }), "_self");
        }
    },

    watch: {
        activeTab(val) {
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + this.tabs.find(x => x.text == val).value;
                window.history.pushState({path:newurl},'',newurl);
            }
        }
    }
};
</script>