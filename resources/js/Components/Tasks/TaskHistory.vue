<template>
    <div>
        <history-filter @change="updateFilter" />

        <div v-if="histories.length === 0">
            <p class="mt-5 text-center">{{ trans('There are no history events yet.') }}</p>
        </div>

        <history-card 
            v-for="(history, index) in histories"
            :key="index"
            :index="index"
            :history="history"
        />

        <v-pagination
            v-model="options.page"
            @input="fetch"
            :total-visible="options.historyPerPage"
            :length="totalHistory"
        />
    </div>
</template>

<script>
import HistoryCard from "@/Components/Shared/HistoryCard";
import HistoryFilter from "@/Components/Shared/HistoryFilter";

export default {
    name: "TaskHistory",
    components: {
        HistoryCard,
        HistoryFilter
    },
    props: {
        task: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            loading: false,
            histories: [],
            totalHistory: null,
            selectedAdmin: null,
            selectedDates: [],
            options: {
                page: 1,
                historyPerPage: 5
            },
        }
    },

    methods: {
        async fetch() {
            await axios.get(this.route('tasks.history', { 
                    task: this.task.id,
                    page: this.options.page,
                    per_page: this.options.historyPerPage,
                    admin: this.selectedAdmin,
                    dates: this.selectedDates
                }))
                .then((resp) => {
                    this.histories = resp.data.data
                    this.totalHistory = resp.data.total
                })
                .catch((error) => {
                    console.error(error)
                })
                .finally(() => {
                    this.loading = false
                });
        },
        updateFilter({adminId, dates}) {
            this.selectedAdmin = adminId
            this.selectedDates = dates
            this.fetch()
        }
    },

    mounted() {
        this.fetch()
    },

    watch: {
        task() {
            this.fetch();
        },
    }
}
</script>