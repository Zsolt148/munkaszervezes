<template>
    <v-row>
        <v-col cols="12">
            <v-alert prominent color="secondary" outlined>
                <v-row align="center">
                    <v-col class="grow">
                        <v-icon class="me-2">
                            mdi-checkbox-marked-outline
                        </v-icon>
                        {{ trans('Vegyél fel magadnak vagy beosztottjaidnak, szabadságot és táppénzt') }}
                    </v-col>

                    <v-col class="shrink">
                        <v-btn  color="primary" @click="createItem">
                            <v-icon>mdi-plus</v-icon>
                            Hozzáadás
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
                @edit="editItem"
                @refresh="fetchData"
                @items="items"
            />
        </v-col>

        <leave-modal
            :is-visible="isVisible"
            :item="selectedItem"
            :types="types"
            @close="close"
        />
    </v-row>
</template>

<script>
import LeavesTable from "@/Components/Tables/Tasks/LeavesTable";
import LeaveModal from "@/Components/Tasks/LeaveModal";

export default {
    name: 'LeavesTab',
    components: {
        LeavesTable,
        LeaveModal
    },
    data() {
        return {
            isVisible: false,

            selectedItem: null,
            loading: true,
            leaves: [],
            years: [],
            types: [],
        };
    },
    created() {
        this.fetchData()
    },
    methods: {
        async fetchData() {
            this.loading = true;
            const resp = await axios.get(this.route('tasks.tab.leaves'))
            this.leaves = resp.data.leaves
            this.types = resp.data.types
            this.years = resp.data.years
            this.loading = false
        },
        createItem() {
            this.item = null
            this.isVisible = true
        },
        editItem(item) {
            this.isVisible = true
            this.selectedItem = item
        },
        close() {
            this.isVisible = false
            this.selectedItem = null
            this.fetchData()
        },
        items(items) {
            this.$emit('items', items)
        }
    },
}
</script>