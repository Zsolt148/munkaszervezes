<template>
    <div class="worker--card" :key="role.value">
        <div class="d-flex">
            <div v-for="user in role.users" class="worker--card-user">
                <div class="worker--card-header">
                    <h5>{{ role.text }}</h5>
                </div>

                <planning-card-heading :user="user" />

                <div
                    :data-date-row="index"
                    :class="{'today': isToday(date), 'weekend' : isWeekend(date)}"
                    v-for="(date, index) in user.current_dates"
                    class="task--selector-body"
                >
                    <v-row class="ma-0">
                        <v-col cols="10">
                            <draggable
                                class="list-group task--selector-body-child"
                                v-model="date.tasks"
                                v-bind="dragOptions"
                                handle=".handle"
                                :disabled="isWeekend(date) ? true : false"
                                :data-card-count="date.tasks.length"
                                :data-date-row="index"
                                @change="onTaskChange($event, user.id, role.value, date.date, index)">
                                <task-card
                                    v-for="task in date.tasks"
                                    :key="task.id"
                                    :task="task"
                                    @fetchData="$emit('fetchData')"
                                />
                            </draggable>
                        </v-col>

                        <v-col class="worker--card-time" cols="2">
                            <span v-if="date.tasks.reduce((acc, task) => acc + task.estimated_hour, 0) > 0">
                                {{ date.tasks.reduce((acc, task) => acc + task.estimated_hour, 0) }} óra
                            </span>
                        </v-col>
                    </v-row>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import TaskCard from "@/Components/ResourcePlanning/TaskCard";
import PlanningCardHeading from "./PlanningCardHeading.vue"
import draggable from "vuedraggable";
import moment from "moment";
import { flash } from "@/Use/helpers"

export default{
    name: "PlaningCard",

    components:{
        draggable,
        TaskCard,
        PlanningCardHeading
    },

    props: {
        role: {
            required: true,
            type: Object
        },
        dates: {
            type: Array
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
        this.setTaskSelectorBodyChildMinHeight(72)
    },

    methods: {
        setTaskSelectorBodyChildMinHeight(heightIncrease) {
            const taskSelectorBodies = document.querySelectorAll('.task--selector-body-child');
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

        onTaskChange(event, userId, roleId, date) {
            if (event && event.hasOwnProperty('added')) {
                this.updateStatus(event.added.element, userId, roleId, date)
                    .catch(error => {
                        console.error(error);
                    });
            }
        },

        isToday(date){
            return moment().isSame(date.date, 'day');
        },

        isWeekend(date){
            const selectedMoment = moment(date.date);
            return selectedMoment.weekday() === 6 || selectedMoment.weekday() === 0;
        },

        async updateStatus(task, userId, roleId, date) {
            await axios.patch(this.route('resource-planning.update', { task: task.id }), {
                userId: userId,
                roleId: roleId,
                date: date
            }).then((resp) => {
                this.$emit('fetchData')
                this.setTaskSelectorBodyChildMinHeight(72);
                flash(this, resp.data)
            }).catch(() => {
                console.error('Unable to update task')
            })
        },
    },

    updated(){
        this.setTaskSelectorBodyChildMinHeight(72);
    },

    watch: {
        isDragging(newValue) {
            if (newValue) {
                this.delayedDragging = true;
                return;
            }
            this.$nextTick(() => {
                this.delayedDragging = false;
            });
        }
    },
}
</script>
