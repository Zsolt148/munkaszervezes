<template>
    <div class="probot--card-rounded mt-5" v-if="task.park">
        <h3 class="probot--card-header">{{ trans('Location') }}</h3>

        <v-row class="probot--card-datas">
            <v-col cols="12" class="pt-0">
                <h3 class="probot--card-park-name">
                    {{ task.park.name }}
                </h3>

                <p class="probot--card-park-loc">
                    {{ task.park.zip }} {{ task.park.city }}  {{ task.park.address }}
                </p>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default{
    name: "TaskLocationCard",
    props:{
        task:{
            type: Object,
            required: true
        }
    }
}
</script>