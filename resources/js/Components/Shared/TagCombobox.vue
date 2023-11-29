<template>
    <v-combobox
        v-model="vmodel"
        :items="entries"
        :loading="isLoading"
        :search-input.sync="search"
        :disabled="disabled"
        :hide-no-data="!search"
        :error-messages="errorMsg"
        @change="onChange"
        :placeholder="trans('Start typing to search')"
        :label="trans(label)"
        :hint="trans(hint)"
        :dense="dense"
        persistent-hint
        hide-details="auto"
        :outlined="outlined"
        :filled="filled"
        multiple
        small-chips
        clearable
    >
        <template v-slot:no-data>
            <v-list-item>
                <span class="mr-2">{{ trans('Create') }}</span>
                <v-chip
                    pill
                    small
                >
                    {{ search }}
                </v-chip>
            </v-list-item>
        </template>
    </v-combobox>
</template>

<script>
export default {
    name: "TagCombobox",
    props: {
        model: {
            required: true
        },
        dense: {
            type: Boolean,
            required: false,
            default: false,
        },
        outlined: {
            type: Boolean,
            required: false,
            default: false,
        },
        filled: {
            type: Boolean,
            required: false,
            default: false,
        },
        lang: {
            required: false,
            type: String,
            default: 'hu',
        },
        type: {
            default: null
        },
        disabled: {
            required: false,
            default: false
        },
        label:{
            required: false,
            default: 'Tags'
        },
        errorMsg:{
            required: false,
            default: null
        },
        hint:{
            required: false,
            default: 'Select an existing tag or type in a new'
        }
    },
    data() {
        return {
            isLoading: false,
            search: null,
            entries: [],
            vmodel: this.model[this.lang],
            timeout: null,
        }
    },

    methods: {
        onChange(items) {
            this.model[this.lang] = items
            this.$emit('update:model', this.model)
        }
    },

    watch: {
        lang(val) {
            this.vmodel = this.model[val]
            this.entries = []
        },
        search (val) {
            // search input is empty
            if (!val) return

            // Items have already been requested
            if (this.isLoading) return

            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {
                this.isLoading = true

                // Lazily load input items
                axios.get(this.route('tags.search', {locale: this.lang, type: this.type, search: val}))
                    .then(res => {
                        this.entries = res.data
                    })
                    .catch(err => {
                        console.error(err)
                    })
                    .finally(() => (this.isLoading = false))
            }, 200)
        },
    },
}
</script>