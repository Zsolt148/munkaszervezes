<template>
    <v-card
        outlined
        class="canban--card mx-auto list-group-item">
        <div>
            <div class="canban--card-header">
                <div class="status--text" :class="task.status">
                    {{ trans(task.statusText) }}
                </div>
                <div class="priority--text">{{ trans(task.priorityText) }}</div>
            </div>
            <div class="canban--card-data">
                <span class="canban--card-id">#{{ task.id }}</span>

                <div class="canban--card-name">
                    {{ task.name }}
                </div>
            </div>
            
            <div class="canban--card-responsible" v-if="task.responsible">
                <v-avatar size="20">
                    <v-img :src="getProfPicture(task.responsible.photo, task.responsible.name)"></v-img>
                </v-avatar>
                {{ task.responsible.name }}
            </div>

            <div class="canban--card-type">
                {{ trans(task.taskTypeText) }}
            </div>

            <div class="canban--card-deadline" v-if="task.deadline">
                <v-icon small>mdi-calendar-outline</v-icon>
                {{ trans('Deadline') }}: {{ dateFormat(task.deadline, 'YYYY.MM.DD') }}
            </div>

            <div class="canban--card-estimated" v-if="task.estimated_hour">
                <v-icon small>mdi-clock-outline</v-icon>
                {{ trans('Estimated time') }}: {{ task.estimated_hour }} {{ trans('hour') }}
            </div>
        </div>
    </v-card>
</template>

<script>
export default {
    name: "TaskCard",
    props: ['task']
}
</script>