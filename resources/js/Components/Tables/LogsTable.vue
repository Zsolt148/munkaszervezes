<template>
    <div>
        <v-row class="mt-0">
            <v-col md="12" cols="12">
                <v-row cols="12" class="mt-0">
                    <v-col cols="12">
                        <div class="table--header">
                            <h1>
                                {{ trans('Logs') }}
                            </h1>
                        </div>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="search"
                    :placeholder="trans('Search')"
                    @input="handleSearch"
                    prepend-inner-icon="mdi-magnify"
                    id="search"
                    ref="search"
                    filled
                    clearable
                    dense
                    hide-details="auto"
                />
            </v-col>
            <v-col col="2">
                <v-btn @click="getLogs" outlined>
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <v-data-table
                    @click:row="showLogModal"
                    :headers="headers"
                    :loading="loading"
                    :items="logs"
                    :options="options"
                    :hide-default-footer="true"
                    item-key="id"
                    :no-data-text="trans('No data')"
                    :no-results-text="trans('No results')"
                    role="button"
                >
                    <template v-slot:item.name="{ item }">
                        <span>
                            <b class="table--bold">{{item.name}} - {{item.event}}</b>
                            <p v-if="item.causer && item.event != 'login'" class="table--small">
                                ({{ item.causer.name }})
                            </p>
                        </span>
                    </template>

                    <template v-slot:item.event="{ item }">
                        <p class="table--field" v-if="item.event">
                            {{trans(item.event)}}
                        </p>
                    </template>

                    <template v-slot:item.created_at="{ item }">
                        <p class="table--field" v-if="item.event">
                            {{ dateFormat(item.created_at) }}
                        </p>
                    </template>
                </v-data-table>

                <div class="d-flex justify-center py-4">
                    <v-select
                        :label="trans('Items per page')"
                        class="pe-4 pt-2"
                        v-model="options.logsPerPage"
                        @change="getLogs"
                        :items="[5, 10, 15, 20]"
                        style="max-width: 100px"
                        dense
                    />
                    <v-pagination
                        class=""
                        v-model="page"
                        @input="getLogs"
                        :total-visible="options.logsPerPage"
                        :length="totalLogs"
                    />
                </div>
            </v-col>
        </v-row>

        <logs-modal
            :isVisible="isLogModalVisible"
            :log="selectedLog"
            @close="hideLogModal"
        />
    </div>
</template>

<script>

import LogsModal from "@/Pages/Logs/LogsModal";
export default {
    name: "LogsTable",
    components: {
        LogsModal
    },
    props: {
        subject: {
            required: false,
            default: null
        },
    },
    data: function () {
        return {
            search: "",
            selectedLog: null,
            isLogModalVisible: false,
            logs: [],
            page: 1,
            loading: true,
            totalLogs: null,
            options: {
                page: 1,
                logsPerPage: 10,
            },
            headers: [
                { text: this.trans("Description"), align: "start", value: "name",},
                { text: this.trans("Event"), value: "event" },
                { text: this.trans("Date"), value: "created_at", align: 'end' },
            ],
        };
    },

    methods: {
        showLogModal(log) {
            this.selectedLog = log;
            this.isLogModalVisible = true;
        },

        hideLogModal() {
            this.isLogModalVisible = false;
        },

        handleSearch: _.debounce(function (e) {
            this.getLogs();
        }, 500),

        async getLogs() {
            this.loading = true;
            this.options.page = this.page;
            await axios.get(this.route('logs.table'), {
                params: {
                    page: this.options.page,
                    per_page: this.options.logsPerPage,
                    search: this.search,
                    subject_type: this.subject ? this.subject.class : null,
                    subject_id: this.subject ? this.subject.id : null,
                }
            }).then((response) => {
                this.logs = response.data.data;
                this.totalLogs = response.data.total;
            })
            .catch((error) => {
                console.log(error);
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },

    created() {
        this.getLogs();
    },
};
</script>