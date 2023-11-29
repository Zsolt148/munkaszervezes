<template>
    <div>
        <v-row>
            <v-col md="3" cols="12">
                <v-select
                    :items="types"
                    v-model="selectedType"
                    :placeholder="trans('Type')"
                    name="type"
                    clearable
                    hide-details="auto"
                    dense
                    filled
                />
            </v-col>
            <v-col md="3" cols="12">
                <v-select
                    :items="years"
                    v-model="selectedYear"
                    :placeholder="trans('Year')"
                    name="type"
                    clearable
                    hide-details="auto"
                    dense
                    filled
                />
            </v-col>
            <v-col col="2">
                <v-btn @click="$emit('refresh')" outlined>
                    <v-icon>mdi-refresh</v-icon>
                </v-btn>
            </v-col>
        </v-row>

        <v-row>
            <v-col cols="12">
                <h2 class="sick--table-title"> {{ profile ? trans('My leaves') : 'Szabadság és táppénz' }} ({{ filteredItems.length }})</h2>
            </v-col>

            <v-col>
                <v-data-table
                    :headers="headers"
                    :items="filteredItems"
                    :loading="loading"
                    item-key="id"
                >
                    <template v-slot:item.id="{ item }">
                        <span class="table--bold">#{{item.id}}</span>
                    </template>

                    <template v-slot:item.admin="{ item }">
                        <v-avatar size="20px" class="me-2">
                            <v-img :src="getProfPicture(item.admin.photo, item.admin.name)"></v-img>
                        </v-avatar>
                        <span class="table--field">{{ item.admin.name }}</span>
                    </template>

                    <template v-slot:item.type="{ item }">
                        <span class="table--field">{{ item.type_text }}</span>
                    </template>

                    <template v-slot:item.date="{ item }">
                        <span class="table--field">{{ item.date_text }}</span>
                    </template>

                    <template v-slot:item.day="{ item }">
                        <span class="table--field">{{ item.day }}</span>
                    </template>

                    <template v-slot:item.status="{ item }">
                        <span :class="`${item.status} leave--status table--field`">
                            <v-icon v-if="item.status === 'done'" color="green" class="me-2">
                                mdi-check-circle
                            </v-icon>
                            {{ item.status_text }}
                        </span>
                    </template>

                    <template v-slot:item.actions="{ item }" v-if="!profile">
                        <v-menu rounded
                        >
                            <template v-slot:activator="{ on, attrs }">
                                <v-btn
                                    icon
                                    v-bind="attrs"
                                    v-on="on"
                                >
                                    <v-icon>mdi-dots-vertical</v-icon>
                                </v-btn>
                            </template>

                            <v-list dense>
                                <v-list-item v-if="!item.accepted_at" @click="accept(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-check</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Accept') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item v-if="!item.declined_at" @click="decline(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-cancel</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Decline') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item @click="editItem(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-pencil-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Edit') }}
                                    </v-list-item-content>
                                </v-list-item>

                                <v-list-item @click="deleteItem(item)">
                                    <v-list-item-icon>
                                        <v-icon>mdi-delete-outline</v-icon>
                                    </v-list-item-icon>
                                    <v-list-item-content>
                                        {{ trans('Delete') }}
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list>
                        </v-menu>
                    </template>
                </v-data-table>
            </v-col>
        </v-row>

        <confirms-modal ref="confirm" v-bind="confirmProps">
            <template v-if="actionType === 'delete' && dateToDelete" v-slot:header>
                <div class="dialog--card pa-5">
                    <div class="d-flex">
                        <v-icon>
                            mdi-calendar
                        </v-icon>
    
                        <h3 class="ms-3">{{ dateToDelete }}</h3>
                    </div>
                </div>
            </template>
        </confirms-modal>
    </div>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";
import {flash} from "@/Use/helpers";

export default {
    name: "LeavesTable",
    props: {
        types: {
            required: true
        },
        years: {
            required: true,
        },
        leaves: {
            required: true
        },
        loading: {
            required: false
        },
        profile: {
            default: false,
            type: Boolean
        }
    },
    components: {
        ConfirmsModal,
    },
    data() {
        return {
            selectedType: null,
            selectedYear: null,
            actionType: null,
            dateToDelete: '',
            actionTypeProps: {
                accept: {
                    title: 'Szabadság elfogadása',
                    content: 'Biztos, hogy elfogadod a szabadságot?',
                    cancelText: 'Mégse',
                    button: 'Szabadság elfogadása'
                },
                reject: {
                    title: 'Szabadság elutasítása',
                    content: 'Biztos, hogy elutasítod a szabadságot?',
                    cancelText: 'Szabadság megtartása',
                    button: 'Szabadság elutasítása'
                },
                delete: {
                    title: 'Szabadság törlése',
                    content: 'Biztos, hogy törölni szeretnéd a szabadságot?',
                    cancelText: 'Szabadság megtartása',
                    button: 'Szabadság törlése'
                }
            },
            confirmProps: {
                title: '',
                cancelText: '',
                content: '',
                button: '',
            },
            headers: [
                { text: this.trans('#'), value: "id" },
                { text: this.trans('Person'), value: "admin" },
                { text: this.trans("Type"), value: "type" },
                { text: this.trans("Date"), value: "date" },
                { text: this.trans("Day"), value: "day" },
                { text: this.trans("Status"), value: "status" },
                !this.profile ? { text: this.trans("Actions"), value: 'actions', sortable: false, align: 'end'} : {},
            ],
        };
    },
    methods: {
        async accept(item) {
            this.setConfirmProps('accept');
            if (await this.$refs.confirm.open()) {
                const resp = await axios.patch(this.route("tasks.leave.accept", item.id));
                flash(this, resp.data)
                this.$emit('refresh')
            }
        },

        async decline(item) {
            this.setConfirmProps('reject');
            if (await this.$refs.confirm.open()) {
                const resp = await axios.patch(this.route("tasks.leave.decline", item.id));
                flash(this, resp.data)
                this.$emit('refresh')
            }
        },

        async deleteItem(item) {
            this.setConfirmProps('delete', item.date_text);
            if (await this.$refs.confirm.open()) {
                const resp = await axios.delete(this.route("tasks.leave.destroy", {leave: item.id}));
                flash(this, resp.data)
                this.$emit('refresh')
            }
        },

        editItem(item) {
            this.$emit('edit', item)
        },

        setConfirmProps(type, date = null){
            this.actionType = type;
            this.dateToDelete = date;
            this.confirmProps = this.actionTypeProps[type] || {};
        }
    },
    computed: {
        filteredItems() {
            let items = this.leaves

            let type = this.selectedType
            if (type) {
                items = items.filter(x => x.type == type)
            }

            let year = this.selectedYear
            if (year) {
                items = items.filter(x => x.year == year)
            }

            this.$emit('items', items)

            return items
        }
    }
}
</script>
<style scoped>
.leave--status {
    &.done {
        color: green;
    }

    &.not_started {
        color: orange;
    }

    &.in_progress {}

    &.rejected {
        color: red;
    }

    &.waiting_for_approve {}
}
</style>