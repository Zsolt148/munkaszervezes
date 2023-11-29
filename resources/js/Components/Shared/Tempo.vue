<template>
    <div>
        <v-card class="pa-4" outlined>
            <v-row class="mt-0">
                <v-col md="12" cols="12">
                    <v-row class="mt-0">
                        <v-col cols="12" class="pt-0">
                            <v-card-title class="table--header">
                                <b>{{trans('Tempo')}}</b>
                            </v-card-title>
                            <v-card-subtitle class="ps-2 pb-0">
                                <div class="date-range">{{weekStartDate}} - {{weekEndDate}}</div>
                                <v-btn
                                    @click="previousWeek"
                                    plain
                                    x-small
                                >
                                    <v-icon>mdi-chevron-left</v-icon>
                                </v-btn>
                                <v-btn
                                    @click="nextWeek"
                                    plain
                                    x-small
                                >
                                    <v-icon>mdi-chevron-right</v-icon>
                                </v-btn>
                            </v-card-subtitle>
                        </v-col>
                    </v-row>
                </v-col>
            </v-row>
            <v-row class="tempo-row">
                <v-col v-for="day in days"
                       :key="day.value"
                       class="custom7cols">
                    <div class="title">{{day.text}}
                        <span class="time-container">
              <v-btn
                  @click="showMeasureModal(day.value)"
                  plain
                  x-small
              >
                  <v-icon>mdi-plus</v-icon>
                </v-btn>

            </span>
                    </div>
                    <div class="logs-container">
                        <div v-for="item in day.items"
                             class="log"
                             @click="showMeasureEditModal(day.value, item)"
                        >
                            <div>{{item.task.name}}</div>
                            <div>{{item.admin.name}} <span class="time-container">({{item.time}} h)</span></div>
                        </div>
                    </div>
                </v-col>
            </v-row>
        </v-card>
        <task-measure-by-date-modal
            v-if="measureModal"
            :isVisible="measureModal"
            :date="selectedDate"
            :form="form"
            :user="user"
            :client="client"
            @close="closeMeasureModal"
            @fetch="fetchData"
        />
    </div>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import TaskMeasureByDateModal from "@/Pages/Tasks/TaskMeasureByDateModal.vue";
import {EventBus} from '@/Components/Shared/eventbus'

export default {
    name: "Tempo",
    props: {
        user: {
            type: Object,
            required: false,
            default: null
        },
        client: {
            type: Object,
            required: false,
            default: null
        },
    },
    components: {
        AppLayout,
        TaskMeasureByDateModal,
    },
    data() {
        return {
            days: [],
            search: '',
            loading: true,
            weekStart: new Date(),
            weekStartDate: '',
            weekEndDate: '',
            measureModal: false,
            selectedDate: '',
            form: {}
        }
    },
    methods: {
        getWeekStartDate(date) {
            let dayOfWeek = date.getDay();
            let diff = date.getDate() - dayOfWeek + (dayOfWeek === 0 ? -6 : 1);
            return new Date(date.setDate(diff));
        },
        getWeekEndDate(date) {
            let dayOfWeek = date.getDay();
            let diff = date.getDate() + (7 - dayOfWeek);
            return new Date(date.setDate(diff));
        },
        getWeekDays(startDate) {
            let days = [];
            for (let i = 0; i < 7; i++) {
                let day = new Date(startDate);
                day.setDate(startDate.getDate() + i);
                days.push(day);
            }
            return days;
        },
        async fetchData() {
            this.loading = true;
            try {
                // Fetch data for the current week
                let startDate = this.getWeekStartDate(this.weekStart);
                let endDate = this.getWeekEndDate(this.weekStart);
                let adminId = this.user !== null ? this.user.id : null
                let clientId = this.client !== null ? this.client.id : null
                let response = await axios.get(this.route('tasks.get-tempo-data'), {
                    params: {
                        start_date: startDate.toISOString().split('T')[0],
                        end_date: endDate.toISOString().split('T')[0],
                        admin_id: adminId,
                        client_id: clientId,
                    }
                });

                let data = response.data;
                let headers = [];
                let days = this.getWeekDays(startDate);
                days.forEach((day) => {
                    let items = [];
                    let weekday = ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'][day.getDay()];

                    data.forEach((item) => {
                        if (new Date(item.date).toDateString() === day.toDateString()) {
                            items.push(item);
                        }
                    });

                    headers.push({
                        text: `${weekday} ${day.getDate()}/${day.getMonth() + 1}`,
                        value: day.toISOString().split('T')[0],
                        items: items
                    });
                });

                this.days = headers;

            } catch (error) {
                console.error(error);
            }
            this.loading = false;

        },
        previousWeek() {
            this.weekStart.setDate(this.weekStart.getDate() - 7);
            this.weekStartDate = this.getWeekStartDate(this.weekStart).toISOString().split('T')[0];
            this.weekEndDate = this.getWeekEndDate(this.weekStart).toISOString().split('T')[0];
            this.fetchData();
        },
        nextWeek() {
            this.weekStart.setDate(this.weekStart.getDate() + 7);
            this.weekStartDate = this.getWeekStartDate(this.weekStart).toISOString().split('T')[0];
            this.weekEndDate = this.getWeekEndDate(this.weekStart).toISOString().split('T')[0];
            this.fetchData();
        },

        showMeasureModal(date) {
            this.selectedDate = date;
            this.form = this.$inertia.form({
                id: null,
                task_id: null,
                time: "",
                date: this.selectedDate,
                description: ''
            });
            this.measureModal = true;
        },

        showMeasureEditModal(date, workLog) {
            this.selectedDate = date;
            this.form = this.$inertia.form({
                id: workLog.id,
                task_id: workLog.task_id,
                time: workLog.time,
                date: workLog.date,
                description: workLog.description
            });
            this.measureModal = true;
        },

        closeMeasureModal() {
            this.measureModal = false;
        },
    },
    created() {
        this.weekStartDate = this.getWeekStartDate(this.weekStart).toISOString().split('T')[0];
        this.weekEndDate = this.getWeekEndDate(this.weekStart).toISOString().split('T')[0];
        this.fetchData();
    }
};
</script>

<style lang="scss">

.date-range {
    float: left;
    margin-right: 10px;
}

.tempo-row {
    display: table;
    border-collapse: collapse;
    width: 100%;
    margin-left: 0;
    margin-right: 0;
    margin-top: 12px;


    .custom7cols {
        display: table-cell;
        width: 14%;
        max-width: 14%;
        flex-basis: 14%;
        min-width: 100px;
        border: 1px solid lightgrey;

        .title {
            text-align: center;
        }

        .logs-container {

            .log {
                margin-top: 12px;
                padding: 5px;
                background: white;
                border: 1px solid grey;
                border-radius: 5px;
                cursor: pointer;
                pointer-events: auto;

                .time-container {
                    float: right;
                }
            }
        }
    }
}

</style>