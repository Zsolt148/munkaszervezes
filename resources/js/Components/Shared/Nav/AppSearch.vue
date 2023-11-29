<template>
    <v-autocomplete
        v-model="model"
        :items="entries"
        :loading="isLoading"
        :search-input.sync="search"
        :label="trans('Search')"
        :placeholder="trans('Start typing to Search')"
        style="width: 100%"
        dense
        outlined
        hide-no-data
        no-filter
        item-text="match"
        item-value="id"
        class="app-bar-search flex-grow-0 mt-6 me-md-4"
        prepend-inner-icon="mdi-magnify"
        return-object
        clearable
    >
        <template v-slot:item="{ item }">
            <small class="pa-0" v-if="item.disabled">{{ item.name }}</small>
            <v-list-item-content v-else>
                <v-list-item-title v-text="item.name"></v-list-item-title>
                <v-list-item-subtitle v-text="item.match"></v-list-item-subtitle>
            </v-list-item-content>
            <v-list-item-action v-if="!item.disabled">
                <v-icon>mdi-arrow-right</v-icon>
            </v-list-item-action>
        </template>
    </v-autocomplete>
</template>

<script>
export default {
    name: "AppSearch",

    data() {
        return {
            model: '',
            isLoading: false,
            entries: [],
            search: null,
            timeout: null,
        }
    },

    watch: {

        model(value) {
            if(value) {
                return this.$inertia.visit(value.route)
            }
        },

        search(value) {
            if (this.timeout) {
                clearTimeout(this.timeout);
            }

            this.timeout = setTimeout(() => {

                this.isLoading = true

                axios
                    .get(this.route('app.search', {
                        keyword: value
                    }))
                    .then(resp => {
                        this.entries = resp.data
                    })
                    .catch(err => {
                        console.error(err)
                    })
                    .finally(() => (this.isLoading = false))

            }, 200);
        }
    },
}
</script>

<style>
    .app-bar-search .v-list-item{
        min-height: 60px!important;
        height: 60px!important;
    }
</style>
