<template>
    <AppLayout>
        <app-head title="Dashboard"/>

        <v-row>
            <v-col class="mt-0" cols="12">
                <v-card outlined class="pa-4">
                    <v-row class="mt-0">
                        <v-col cols="12" class="pt-0">
                            <v-card-title class="pt-1 ps-2">
                                {{ trans("Dashboard") }}
                            </v-card-title>

                            <v-card-subtitle class="ps-2 pb-0">
                                {{ trans("Statistics and other metrics.") }}
                            </v-card-subtitle>
                        </v-col>
                    </v-row>
                </v-card>
            </v-col>
            <v-col class="mt-0" cols="12">
                <v-row>
                    <v-col cols="12">
                        <v-card class="mt-4" outlined>
                            <v-row class="ma-0 pa-0">
                                <v-col cols="12">
                                    <v-card-title class="pt-1 ps-2 mb-0 ">
                                        {{ trans("Current status of website") }}
                                    </v-card-title>

                                    <v-card-subtitle class="ps-2 mb-0 pb-3">
                                        {{ trans("In maintenance mode, visitors will not reach the website") }}
                                    </v-card-subtitle>

                                    <v-divider></v-divider>

                                    <v-card-text class="text-center pt-5">
                                        <div v-if="$page.props.page.maintenance">
                                            <v-icon color=primary x-large>
                                                mdi-alert
                                            </v-icon>
                                            <h3 class="pt-3">{{ trans("Under maintenance") }}</h3>
                                        </div>
                                        <div v-else>
                                            <v-icon color=primary x-large>
                                                mdi-check-circle
                                            </v-icon>
                                            <h3 class="pt-3">{{ trans("Everything operates normally") }}</h3>
                                        </div>
                                    </v-card-text>
                                </v-col>
                            </v-row>
                        </v-card>
                    </v-col>

                    <v-col cols="12" md="6">
                        <statistic-card
                            :title="trans('Users statistics')"
                            :subtitle="trans('Statistics about the webpage')"
                            :statistics="userStats"
                        />
                    </v-col>
                    <v-col cols="12" md="6">
                        <statistic-card
                            :title="'Feladat statisztika'"
                            :subtitle="'Feladatkezelő statisztikái'"
                            :statistics="generalStats"
                        />
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </AppLayout>
</template>
<script>

import AppLayout from "@/Layouts/AppLayout";
import BaseCard from "@/Components/Cards/BaseCard.vue";
import StatisticCard from "@/Components/Cards/StatisticCard.vue";

export default {
    name: "Dashboard",
    props: [
        'sumOfUsers',
        'registeredUsersCount',
        'invitedUsersCount',
        'blockedUsersCount',
    ],
    components: {
        AppLayout,
        BaseCard,
        StatisticCard,
    },

    data() {
        return {
            loading: false,
            userStats: [
                {
                    icon: 'mdi-account-multiple',
                    title: this.trans('All users'),
                    value: ''
                },
                {
                    icon: 'mdi-account',
                    title: this.trans('Registered'),
                    value: ''
                },
                {
                    icon: 'mdi-account',
                    title: this.trans('Invited'),
                    value: ''
                },
            ],
            generalStats: [
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('Tasks'),
                    value: ''
                },
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('Todo'),
                    value: ''
                },
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('In progress'),
                    value: ''
                },
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('Waiting'),
                    value: ''
                },
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('Waiting for repair'),
                    value: ''
                },
                {
                    icon: 'mdi-checkbox-multiple-marked',
                    title: this.trans('Done'),
                    value: ''
                },
            ],
        }
    },

    methods: {
        async fetch() {
            this.loading = true;
            try {
                const response = await axios.get(this.route('api:dashboard.fetch'));
                const data = response.data;

                this.userStats[0].value = data.sumOfUsers;
                this.userStats[1].value = data.registeredUsers;
                this.userStats[2].value = data.invitedUsers;
                this.userStats[3].value = data.blockedUsers;

                this.generalStats[0].value = data.sumOfTasks;
                this.generalStats[1].value = data.todoTasks;
                this.generalStats[2].value = data.inprogressTasks;
                this.generalStats[3].value = data.waitingTasks;
                this.generalStats[4].value = data.repairTasks;
                this.generalStats[5].value = data.doneTasks;

            } catch (error) {
                console.error(error);
            }
            this.loading = false;
        },
    },

    mounted() {
        this.fetch();
    }
}
</script>
