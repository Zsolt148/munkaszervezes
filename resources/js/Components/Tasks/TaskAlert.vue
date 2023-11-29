<template>
    <div>
        <v-alert v-if="task.status == taskStatuses.done" class="mt-5" prominent type="success" outlined>
            <v-row align="center">
                <v-col class="grow">
                    {{ trans('Task is finished') }}
                    <span v-if="task.responsible">
                        {{ trans('by:') }} {{ task.responsible.name }} {{ task.done_at }}
                    </span>
                </v-col>
            </v-row>
        </v-alert>

        <v-alert v-if="task.status == taskStatuses.inProgress" class=" mt-5" prominent type="info" outlined>
            <v-row align="center">
                <v-col class="grow">
                    {{ trans('If you have completed the task, record the time spent on the work.') }}
                </v-col>

                <v-col class="shrink">
                    <v-btn color="primary" @click="$emit('recordTime')">
                        <v-icon class="me-2">
                            mdi-timelapse
                        </v-icon>
                        {{ trans('Record time') }}
                    </v-btn>
                </v-col>
            </v-row>
        </v-alert>
    </div>
</template>

<script>
export default {
    name: "TaskAlert",
    props: {
        task: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            taskStatuses: {
                done: 'done',
                waitingForRepair: 'waiting_for_repair',
                toDo: 'todo',
                inProgress: 'in_progress'
            }
        }
    }
}
</script>