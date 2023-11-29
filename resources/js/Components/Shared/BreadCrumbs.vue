<template>
    <v-row class="my-1 pa-3">
        <div v-for="(item, key) in items" :key="key" class="text-sm">
            <a v-if="item.href && !item.disabled" @click="$inertia.visit((item.route == true) ? item.href : route(item.href))">
                {{ trans(item.text) }}
            </a>
            <span v-else>
                {{ trans(item.text) }}
            </span>
            <span v-if="!lastElement(item)" class="mx-3">
                {{ divider }}
            </span>
        </div>
    </v-row>
</template>

<script>
export default {
    name: "BreadCrumbs",

    props: {
        divider: {
            type: String,
            default: '-',
            required: false,
        },
        items: {
            type: Array,
            required: true,
        }
    },

    methods: {
        lastElement(item) {
            return this.items[this.items.length - 1].text == item.text
        }
    }
}
</script>

<style scoped>

</style>
