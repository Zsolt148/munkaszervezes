<template>
    <section style="flex-direction:column" class="resource--planning">
        <v-row class="mb-3">
            <v-col cols="4">
                <view-picker :views="views" @change="updateViewPicker"/>
            </v-col>

            <v-col md="4">
                <v-select
                    :items="allRoles"
                    :placeholder="trans('Group')"
                    v-model="selectedRole"
                    @change="fetchData"
                    z-index="15"
                    clearable
                    hide-details="auto"
                    dense
                    filled/>
            </v-col>
        </v-row>

        <div class="d-flex" v-if="loading">
            <v-progress-circular indeterminate size="64" class="ma-auto"/>
        </div>
        <div class="d-flex mx-1" v-else>
            <v-card elevation="1" class="task--selector">
                <v-card-title>
                    Feladatok ({{ filteredCompTasks.length }})
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="search"
                        :label="trans('Search')"
                        class="mb-3"
                        filled
                        dense
                        hide-details="auto"
                        item-text="text"
                        item-value="order"
                        clearable
                    />
                    <div class="task--selector-body py-2 px-2">
                        <draggable
                            v-if="filteredCompTasks.length"
                            v-model="filteredCompTasks"
                            v-bind="dragOptions"
                            class="dragArea list-group">
                            <task-card
                                class="list-group-item"
                                v-for="task in filteredCompTasks"
                                v-show="task"
                                :key="task.id"
                                :task="task"
                            />
                        </draggable>
                    </div>
                </v-card-text>
            </v-card>
            <div class="worker--container">
                <div class="dates--container">
                    <v-col v-if="dates[0] && dates[0].datePeriod" style="height:85px;" class="date-col first">
                        <div class="dates">
                            {{ dates[0].datePeriod }}
                        </div>
                    </v-col>

                    <v-col class="date-col" v-for="(date, index) in dates" :key="index">
                        <div class="dates" :data-date-row="index">
                            <p>{{ date.day }}</p>
                            <small>{{ date.dateDay }}</small>
                        </div>
                    </v-col>
                </div>

                <planning-card
                    v-for="role in roles"
                    @fetchData="fetchData"
                    :role="role"
                    :key="role.value"/>
            </div>
        </div>
    </section>
</template>

<script>
import TaskCard from "@/Components/ResourcePlanning/TaskCard";
import PlanningCard from "@/Components/ResourcePlanning/PlanningCard.vue";
import moment from "moment/moment";
import draggable from "vuedraggable";
import ViewPicker from "@/Components/Shared/ViewPicker";

export default {
    name: 'PlanningTab',

    components: {
        draggable,
        TaskCard,
        PlanningCard,
        ViewPicker
    },

    props: ['views', 'refreshPending', 'allRoles'],

    data() {
        return {
            orderOptions: [
                {
                    order: 'priority',
                    text: this.trans('Order by priority')
                },
                {
                    order: 'date',
                    text: this.trans('Order by date')
                },
                {
                    order: 'created_at',
                    text: this.trans('Order by created at')
                }
            ],
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost",
                scrollSensitivity: 500,
                forceFallback: false
            },
            loading: true,
            search: '',
            selectedRole: null,
            filteredRoles: [],
            compTasks: [],
            roles: [],
            dates: [],
            selectedDay: null,
            selectedWeek: [
                moment().startOf('week').add(1, 'd').format('YYYY-MM-DD'),
                moment().endOf('week').add(1, 'd').format('YYYY-MM-DD')
            ],
            selectedMonth: null,
        }
    },

    methods: {
        async fetchData() {
            await axios.get(this.route('resource-planning.fetch-workers'), {
                params: {
                    day: this.selectedDay,
                    week: this.selectedWeek,
                    month: this.selectedMonth,
                    role: this.selectedRole,
                }
            }).then((resp) => {
                this.tags = resp.data.tags
                this.compTasks = resp.data.tasks
                this.roles = resp.data.roles
                this.dates = resp.data.dates
            })
        },

        updateViewPicker({day, week, month}) {
            this.selectedDay = day
            this.selectedWeek = week
            this.selectedMonth = month
            this.fetchData()
        },
    },

    async created() {
        this.loading = true
        await this.fetchData();
        this.loading = false
    },

    computed: {
        filteredCompTasks: {
            get() {
                let cards = Array.from(this.compTasks);
                let search = this.search;
                if (search) {
                    return cards.filter(card => card.name.toLowerCase().indexOf(search) > -1);
                }

                return cards;
            },

            set() {

            }
        }
    },

    watch: {
        refreshPending(val) {
            if (val) {
                this.fetchData()
                this.$emit('refreshed')
            }
        }
    }
}
</script>