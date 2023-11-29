<template>
    <v-subheader>
        <span class="title-wrapper">
            <span>{{ title }}</span>
        </span>
    </v-subheader>
</template>
    
<script>
    export default {
        props: {
            title: {
                type: String,
                required: true,
            },
        },
    }
</script>
    