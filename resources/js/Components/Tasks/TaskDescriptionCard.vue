<template>
    <div class="probot--card nopad mt-5">
        <h3 class="probot--card-header">{{ trans('Task description') }}</h3>

        <div class="probot--card-description" v-html="task.description"></div>
    </div>
</template>

<script>
export default{
    name: "TaskDescriptionCard",
    props:{
        task:{
            type: Object,
            required: true
        }
    }
}
</script>