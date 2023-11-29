<template>
    <div class="probot--card mt-5" v-if="task.tags">
        <h3 class="probot--card-header">{{ trans('Tags') }}</h3>

        <v-chip
            label 
            color="primary" 
            class="me-2" v-for="tag in task.tags['hu']">
            {{ tag }}
        </v-chip>
    </div>
</template>

<script>
export default{
    name: "TaskTagsCard",
    props:{
        task:{
            type: Object,
            required: true
        }
    }
}
</script>