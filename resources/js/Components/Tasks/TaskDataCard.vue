<template>
    <div class="probot--card">
        <h3 class="probot--card-header">{{ trans('Task') }} #{{ task.id }}</h3>

        <v-row class="probot--card-datas">
            <v-col cols="12" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Task description') }}</h5>
                    <p>{{ task.name }}</p>
                </div>
            </v-col>

            <v-col cols="12" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Park') }}</h5>
                    <p>{{ task.park.name }}</p>
                </div>
            </v-col>

            <v-col cols="12" md="6" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Task status') }}</h5>
                    <p>{{ task.statusText }}</p>
                </div>
            </v-col>

            <v-col cols="6" md="6" class="pt-0">
                <div class="probot--card-data">
                    <h5><v-icon small class="me-1">mdi-calendar</v-icon> {{ trans('Deadline') }}</h5>
                    <p>{{ task.deadline }}</p>
                </div>
            </v-col>

            <v-col cols="6" md="6" class="pt-0">
                <div class="probot--card-data">
                    <h5><v-icon small class="me-1">mdi-clock-outline</v-icon>{{ trans('Estimated time') }}</h5>
                    <p>{{ task.estimated_hour }} {{ trans('hour')}}</p>
                </div>
            </v-col>

            <v-col cols="6" class="pt-0">
                <div class="probot--card-data outlined">
                    <h5>{{ trans('Time spent') }}</h5>
                    <p>{{ task.working_hours_sum }} {{ trans('hour') }}</p>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default{
    name: "TaskDataCard",
    props:{
        task:{
            type: Object,
            required: true
        }
    }
}
</script>