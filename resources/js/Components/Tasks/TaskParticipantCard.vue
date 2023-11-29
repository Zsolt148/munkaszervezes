<template>
    <div class="probot--card">
        <h3 class="probot--card-header">{{ trans('Participants') }}</h3>

        <v-row class="probot--card-datas">
            <v-col cols="12" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Group') }}</h5>
                    <p>{{ task.roleText ? task.roleText : '-'}}</p>
                </div>
            </v-col>

            <v-col cols="12" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Responsible') }}</h5>
                    <p>{{ task.responsible ? task.responsible.name : '-' }}</p>
                </div>
            </v-col>

            <v-col cols="12" class="pt-0">
                <div class="probot--card-data">
                    <h5>{{ trans('Watchers') }}</h5>
                    <p>{{ task.watchersText ? task.watchersText : '-' }}</p>
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default{
    name: "TaskParticipantCard",
    props:{
        task:{
            type: Object,
            required: true
        }
    }
}
</script>