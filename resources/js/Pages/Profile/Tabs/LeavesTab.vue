<template>
    <v-row>
        <v-col cols="12">
            <v-alert type="primary" outlined prominent>
                <v-row align="center">
                    <v-col class="grow">
                        <v-icon class="me-2">
                            mdi-checkbox-multiple-marked
                        </v-icon>

                        <b class="mx-2">{{ trans('Task manager') }}</b>

                        Kattints a gombra ha szabadságot vagy táppénzt szeretnél felvenni magadnak.
                    </v-col>

                    <v-col class="shrink">
                        <v-btn color="primary" class="d-block ms-auto" @click="$inertia.visit(route('tasks.index', {tab:'leaves'}))">
                            {{ trans('All vacation and sick pay') }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-alert>
        </v-col>
        <v-col md="12">
            <leaves-table
                :types="types"
                :years="years"
                :leaves="leaves"
                :loading="loading"
                :profile="true"
                @refresh="fetchData"
            />
        </v-col>
    </v-row>
</template>

<script>
import LeavesTable from "@/Components/Tables/Tasks/LeavesTable";

export default {
    name: 'LeavesTab',
    components: {
        LeavesTable
    },
    data() {
        return {
            loading: true,
            leaves: [],
            types: [],
            years: [],
        };
    },
    created() {
        this.fetchData()
    },
    methods: {
        async fetchData() {
            this.loading = true;
            const resp = await axios.get(this.route('profile.leaves'))
            this.leaves = resp.data.leaves
            this.years = resp.data.years
            this.types = resp.data.types
            this.loading = false
        },
    },
}
</script>