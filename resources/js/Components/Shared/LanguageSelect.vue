<template>
    <component :is="component" v-show="items.length > 1" :cols="cols" :md="md">
        <v-select
            v-model="language"
            @change="$emit('update:lang', language)"
            :label="trans(label)"
            :dense="dense"
            filled
            id="language"
            name="language"
            hide-details="auto"
            :items="items"
            :error-messages="errorMessages ? trans('Error in an other langague') : ''"
        ></v-select>
    </component>
</template>

<script>
export default {
    name: "LanguageSelect",
    props: {
        lang: {
            required: true,
            default: 'hu'
        },
        label: {
            required: false,
            default: 'Language'
        },
        errors: {
            required: false,
            default: ''
        },
        cols: {
            required: false,
            default: null
        },
        md: {
            required: false,
            default: null
        },
        dense: {
            required: false,
            default: false
        }
    },
    data() {
        return {
            items: this.$page.props.locales,
            language: this.lang,
        }
    },
    computed: {
        component() {
            return (this.cols || this.md) ? 'v-col' : 'div'
        },
        errorMessages() {
            return Object.keys(this.errors).length
        }
    }
}
</script>