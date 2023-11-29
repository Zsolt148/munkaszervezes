<template>
    <div class="worker--card" style="display:flex; flex-direction:column; max-height:unset" :key="role.value">
        <div class="d-flex" style="display:flex; flex-direction:column;">
            <div v-for="user in role.users" class="worker--card-user">
                <div class="worker--card-header">
                    <h5>{{ role.text }}</h5>
                </div>

                <pre-planning-card-heading :user="user" />

                <div 
                    :data-date-row="`${index}-${variantId}`"
                    :class="{'today': isToday(date), 'd-none' : isWeekend(date)}"
                    v-for="(date, index) in user.current_dates" 
                    class="task--selector-body"
                >

                    <v-row class="ma-0">
                        <v-col cols="10">
                            <draggable
                                class="list-group task--selector-body-child"
                                v-model="date.planned_tasks"
                                v-bind="dragOptions"
                                handle=".handle"
                                :disabled="true"
                                :data-card-count="date.planned_tasks.length"
                                :data-date-row="`${index}-${variantId}`"
                                :data-card-id="variantId"
                                @change="onTaskChange($event, user.id, role.value, date.date, index)">
                                <i class="text-center d-block">{{ date.date }}</i>
                                <pre-task-card
                                    v-for="task in date.planned_tasks"
                                    :key="task.task_id"
                                    :variant-id="variantId"
                                    :task-id="task.task_id"
                                    :task="task.task"
                                />
                            </draggable>
                        </v-col>
                        
                        <v-col class="worker--card-time" cols="2">
                            <span v-if="date.planned_tasks.reduce((acc, task) => acc + task.task.estimated_hour, 0) > 0">
                                {{ date.planned_tasks.reduce((acc, task) => acc + task.task.estimated_hour, 0) }} óra
                            </span>
                        </v-col>
                    </v-row>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import PreTaskCard from "./PreTaskCard.vue";
import PrePlanningCardHeading from "@/Components/ResourcePlanning/Print/PrePlanningCardHeading"
import draggable from "vuedraggable";
import moment from "moment";

export default{
    name: "PrePlanningCard",

    components:{
        draggable,
        PreTaskCard,
        PrePlanningCardHeading
    },

    props: {
        role: {
            required: true,
            type: Object
        },
        dates: {
            type: Array
        },
        variantId:{
            required: true
        }
    },

    data() {
        return {
            model: [],
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost",
                scrollSensitivity: 300,
                forceFallback: false,
                disabled: true
            },
        }
    },

    mounted() {
        setTimeout(() => {
            this.setTaskSelectorBodyChildMinHeight(84, this.variantId)
        }, 300);
    },

    methods: {
        setTaskSelectorBodyChildMinHeight(heightIncrease, id) {
            const taskSelectorBodies = document.querySelectorAll(`.task--selector-body-child`);
            if (taskSelectorBodies.length === 0) {
                return;
            }
            taskSelectorBodies.forEach(body => {
                const dateRow = body.getAttribute('data-date-row');
                if (!dateRow) {
                    return;
                }
                let maxCardCount = 0;
                taskSelectorBodies.forEach(otherBody => {
                    const otherDateRow = otherBody.getAttribute('data-date-row');
                    if (otherDateRow === dateRow) {
                        const otherCardCount = parseInt(otherBody.getAttribute('data-card-count'), 10);
                        if (!isNaN(otherCardCount) && otherCardCount > maxCardCount) {
                            maxCardCount = otherCardCount;
                        }
                    }
                });

                const minHeight = (maxCardCount * heightIncrease) + 'px';
                body.style.height = minHeight;

                const dateDiv = document.querySelector(`.dates[data-date-row="${dateRow}"]`);
                if (dateDiv) {
                    dateDiv.style.height = minHeight;
                }
            });
        },

        isToday(date){
            return moment().isSame(date.date, 'day');
        },

        isWeekend(date){
            const selectedMoment = moment(date.date);
            return selectedMoment.weekday() === 6 || selectedMoment.weekday() === 0;
        },
    },

    watch: {
        variantId(newValue) {
            setTimeout(() => {
                this.setTaskSelectorBodyChildMinHeight(84, newValue)
            }, 300);
        }
    },
}
</script>
