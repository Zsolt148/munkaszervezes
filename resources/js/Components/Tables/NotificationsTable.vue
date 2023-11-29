<template>
    <div>
        <v-row cols="12" class="mt-5">
            <v-col cols="12">
                <h1>
                    {{ trans('Notifications') }}
                </h1>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12" md="4">
                <v-text-field
                    v-model="search"
                    :placeholder="trans('Search')"
                    prepend-inner-icon="mdi-magnify"
                    id="search"
                    ref="search"
                    filled
                    clearable
                    dense
                    hide-details="auto"
                />
            </v-col>
        </v-row>

        <v-row>
            <v-col>
                <v-data-table
                    :headers="headers"
                    :loading="loading"
                    :items="items"
                    :options="options"
                    :hide-default-footer="true"
                    :item-class="isRead"
                    item-key="id"
                    :no-data-text="trans('No data')"
                    :no-results-text="trans('No results')"
                    role="button">
                    <template v-slot:item.title="{ item }">
                        <div @click="showItem(item)" class="d-flex flex-column">
                            <span class="font-weight-bold">
                                {{ item.data.title }}
                            </span>
                            <span class="">
                                {{ item.data.body }}
                            </span>
                        </div>
                    </template>


                    <template v-slot:item.created_at="{ item }">
                        <div class="d-flex flex-column">
                            <span class="table--field">
                                {{ item.created_at_time }}
                            </span>
                            <small class="table--small">
                                {{ item.created_at_date }}
                            </small>
                        </div>
                    </template>

                    <template v-slot:item.actions="{ item, index }">
                        <div style="gap:8px;" class="ms-auto d-flex justify-end">
                            <v-btn
                                v-if="!item.read_at" @click="readItem(item)"
                                small
                                outlined
                                width="250"
                                filled
                                color="primary">
                                <v-icon class="me-2">mdi-check</v-icon>
                                {{ trans('Mark as read') }}
                            </v-btn>

                            <v-btn
                                v-else @click="unreadItem(item)"
                                small
                                outlined
                                filled
                                width="250"
                                color="primary">
                                <v-icon class="me-2">mdi-message-badge-outline</v-icon>
                                {{ trans('Mark as unread') }}
                            </v-btn>

                            <v-btn
                                v-if="!item.trashed" @click="deleteItem(item, index)"
                                small
                                outlined
                                filled
                                color="primary">
                                <v-icon class="me-2">mdi-delete-outline</v-icon>
                                {{ trans('Delete') }}
                            </v-btn>
                        </div>
                    </template>

                </v-data-table>

                <div class="d-flex justify-end py-4">
                    <v-select
                        :label="trans('Items per page')"
                        class="pe-4 pt-2"
                        v-model="options.perPage"
                        @change="fetch"
                        :items="[5, 10, 15, 20]"
                        style="max-width: 100px"
                        dense
                    />
                    <v-pagination
                        v-model="page"
                        @input="fetch"
                        :total-visible="options.perPage"
                        :length="total"
                    />
                </div>
            </v-col>
        </v-row>
    </div>
</template>

<script>
export default {
    name: "NotificationsTable",
    props: {
        notifiable: {
            required: true,
            default: null
        }
    },
    data: function () {
        return {
            search: "",
            items: [],
            page: 1,
            loading: true,
            total: null,
            options: {
                page: 1,
                perPage: 10,
            },
            form: this.$inertia.form(),
            headers: [
                {text: this.trans("Notification type / Task"), width: "500px", align: "start", value: "title",},
                {text: this.trans("Notification time"), value: "created_at"},
                {text: this.trans('Actions'), value: 'actions', sortable: false, align: 'end'},
            ],
        };
    },

    methods: {
        showItem(item) {
            this.$inertia.visit(this.route('notifications.show', item.id))
        },

        readItem(item) {
            this.form.post(this.route('notifications.read', item.id), {
                preserveScroll: true,
            })
            item.read_at = new Date()
        },

        unreadItem(item) {
            this.form.post(this.route('notifications.unread', item.id), {
                preserveScroll: true,
            })
            item.read_at = null
        },

        deleteItem(item, index) {
            this.form.delete(this.route('notifications.delete', item.id), {
                preserveScroll: true,
            })
            this.items.splice(index, 1)
        },

        isRead(item) {
            return item.read_at == null ? '' : 'row--read'
        },

        handleSearch: _.debounce(function (e) {
            this.fetch();
        }, 500),

        async fetch() {
            this.loading = true;
            this.options.page = this.page;
            await axios.get(this.route('notifications.fetch-all'), {
                params: {
                    page: this.options.page,
                    per_page: this.options.perPage,
                    search: this.search,
                    notifiable_type: this.notifiable ? this.notifiable.class : null,
                    notifiable_id: this.notifiable ? this.notifiable.id : null,
                }
            })
                .then((response) => {
                    this.items = response.data.data;
                    this.total = response.data.total;
                })
                .catch((error) => {
                    console.log(error);
                }).finally(() => {
                    this.loading = false;
                });
        },
    },

    created() {
        this.fetch();
    },
};
</script>
