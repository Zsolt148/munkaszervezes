<template>
    <app-layout>
        <app-head title="Profile" />
        <v-row cols="12" class="my-5">
            <v-col cols="12">
                <div class="d-flex">
                    <h1>
                        {{ trans('Profile') }}
                    </h1>
                    <v-spacer></v-spacer>
                    <div>
                        <v-btn 
                            @click="logout"
                            filled
                            class="ms-md-2"
                            color="primary">
                            <v-icon class="me-2">mdi-logout</v-icon> {{trans("Logout")}}
                        </v-btn>
                    </div>
                </div>
            </v-col>
        </v-row>

        <v-tabs hide-arrows v-model="activeTab" class="mb-5">
            <v-tab id="tab--overview">{{ trans('Account data') }}</v-tab>
            <v-tab>{{ trans('Notifications') }}</v-tab>
            <v-tab>{{ trans('Own tasks') }}</v-tab>
            <v-tab id="tab--security">{{ trans('Holiday and sick pay') }}</v-tab>
        </v-tabs>

        <v-tabs-items v-model="activeTab">
            <v-tab-item>
                <overview-tab :user="$page.props.auth.user" />
            </v-tab-item>

            <v-tab-item>
                <notifications-tab :user="$page.props.auth.user" />
            </v-tab-item>

            <v-tab-item>
                <tasks-tab />
            </v-tab-item>

            <v-tab-item >
                <leaves-tab />
            </v-tab-item>
        </v-tabs-items>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import OverviewTab from "./Tabs/OverviewTab";
import TasksTab from "./Tabs/TasksTab";
import LeavesTab from "./Tabs/LeavesTab";
import NotificationsTab from "@/Pages/Profile/Tabs/NotificationsTab";

export default {
    name: 'ProfileShow',
    components: {
        AppLayout,
        OverviewTab,
        TasksTab,
        LeavesTab,
        NotificationsTab
    },
    props: ['sessions', 'tabs', 'tab', 'date_time_formats'],

    mounted() {
        const params = new Proxy(new URLSearchParams(window.location.search), {
            get: (searchParams, prop) => searchParams.get(prop),
        });
    },

    data(){
        return{
            activeTab: this.tab,
            logoutForm: this.$inertia.form()
        }
    },

    methods:{
        logout() {
            this.logoutForm.post(this.route('logout'))
        },
    },

    watch: {
        activeTab(val) {
            if (val == 0) {
                this.refreshGroup = true
            }
            if (val == 1) {
                this.refreshWorker = true
            }

            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + this.tabs.find(x => x.text == val).value;
                window.history.pushState({path:newurl},'',newurl);
            }
        }
    }
}
</script>
