<template>
    <v-dialog v-model="isVisible" max-width="600" persistent @click:outside="close">
        <v-card>
            <v-toolbar>
                <h3 class="modal--title">
                    {{ item ? trans('Edit leave') : trans('Add leave') }}
                </h3>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>

            <div class="modal--body">
                <v-form v-model="valid" ref="form">
                    <v-row>
                        <v-col cols="12">
                            <v-autocomplete
                                v-model="admin"
                                label="Munkatárs"
                                :loading="isLoading"
                                :disabled="item"
                                :items="items"
                                :placeholder="trans('Start typing to search')"
                                :search-input.sync="search"
                                :rules="rules"
                                item-text="name"
                                item-value="id"
                                clearable
                                return-object
                                filled
                                hide-no-data
                                hide-details
                            >
                                <template v-slot:item="{ item }">
                                    <v-list-item-avatar>
                                        <v-img :src="getProfPicture(item.profile_photo_url, item.name)"></v-img>
                                    </v-list-item-avatar>
                                    <v-list-item-content>
                                        <v-list-item-title>{{ item.name }}</v-list-item-title>
                                    </v-list-item-content>
                                </template>
                            </v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                            <v-select
                                v-model="type"
                                :items="types"
                                :label="trans('Type')"
                                :rules="rules"
                                filled
                                hide-details
                            />
                        </v-col>
                    </v-row>
                    <v-row v-if="!item">
                        <v-col cols="6">
                            <v-menu
                                ref="menu1"
                                v-model="menu1"
                                :close-on-content-click="false"
                                :return-value.sync="from"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="from"
                                        label="Dátum-tól"
                                        prepend-inner-icon="mdi-calendar"
                                        :rules="rules"
                                        filled
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="from"
                                    first-day-of-week="1"
                                    no-title
                                    locale="hu-hu"
                                    :min="today"
                                    :max="to"
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu1 = false"
                                    >
                                        {{ trans('Cancel') }}
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.menu1.save(from)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                        <v-col cols="6">
                            <v-menu
                                ref="menu2"
                                v-model="menu2"
                                :close-on-content-click="false"
                                :return-value.sync="from"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="to"
                                        label="Dátum-ig"
                                        prepend-inner-icon="mdi-calendar"
                                        :rules="rules"
                                        filled
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="to"
                                    first-day-of-week="1"
                                    no-title
                                    locale="hu-hu"
                                    :min="from"
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu2 = false"
                                    >
                                        {{ trans('Cancel') }}
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.menu2.save(from)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                    <v-row v-else>
                        <v-col cols="12">
                            <v-menu
                                ref="menu3"
                                v-model="menu3"
                                :close-on-content-click="false"
                                :return-value.sync="date"
                                transition="scale-transition"
                                offset-y
                                min-width="auto"
                            >
                                <template v-slot:activator="{ on, attrs }">
                                    <v-text-field
                                        v-model="date"
                                        label="Dátum"
                                        prepend-inner-icon="mdi-calendar"
                                        :rules="rules"
                                        filled
                                        readonly
                                        v-bind="attrs"
                                        v-on="on"
                                    ></v-text-field>
                                </template>
                                <v-date-picker
                                    v-model="date"
                                    first-day-of-week="1"
                                    :min="today"
                                    no-title
                                    locale="hu-hu"
                                    scrollable
                                >
                                    <v-spacer></v-spacer>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="menu3 = false"
                                    >
                                        {{ trans('Cancel') }}
                                    </v-btn>
                                    <v-btn
                                        text
                                        color="primary"
                                        @click="$refs.menu3.save(date)"
                                    >
                                        OK
                                    </v-btn>
                                </v-date-picker>
                            </v-menu>
                        </v-col>
                    </v-row>
                    <div class="d-flex">
                        <v-btn
                            @click="close"
                            outlined
                            color="secondary">
                            {{ trans('Cancel') }}
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            @click="save"
                            :loading="loading"
                            color="primary">
                            {{ trans('Save') }}
                        </v-btn>
                    </div>
                </v-form>
            </div>
        </v-card>
    </v-dialog>
</template>

<script>
import moment from "moment";
import {flash} from "@/Use/helpers";

export default {
    name: "LeaveModal",
    emits: ['close'],
    props: {
        item: { type: Object, default: null },
        types: Array,
        isVisible: Boolean,
    },
    data() {
        return {
            loading: false,
            isLoading: false,
            valid: false,
            rules: [
                v => !!v || this.trans('Required field')
            ],
            items: [],
            search: null,
            timeout: null,
            menu1: null,
            menu2: null,
            menu3: null,
            admin: null,
            type: 'leave',
            today: moment().format('YYYY-MM-DD'),
            from: moment().add(1, 'd').format('YYYY-MM-DD'),
            to: moment().add(1, 'd').format('YYYY-MM-DD'),
            date: null,
        }
    },
    methods: {
        async save() {
            if (!this.$refs.form.validate()) {
                return
            }

            var resp = {}
            this.loading = true

            if (this.item) {
                // update
                resp = await axios.patch(this.route('tasks.leave.update', this.item.id), {
                    admin_id: this.admin.id,
                    type: this.type,
                    date: this.date
                })
            }else {
                // store
                resp = await axios.post(this.route('tasks.leave.store'), {
                    admin_id: this.admin.id,
                    type: this.type,
                    dates: [this.from, this.to]
                })
            }

            flash(this, resp.data)
            this.loading = false
            this.close()
        },
        close() {
            this.items = []
            this.admin = null
            this.type = 'leave'
            this.date = null
            this.form = moment().add(1, 'd').format('YYYY-MM-DD'),
            this.to = moment().add(1, 'd').format('YYYY-MM-DD'),
            this.$emit("close");
        },
    },
    watch: {
        item(val) {
            if (val) {
                this.items = [val.admin]
                this.admin = val.admin
                this.type = val.type
                this.date = val.date
            }
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
                axios.get(this.route('admins.search', {search: val}))
                    .then(res => {
                        this.items = res.data
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

<style scoped>

</style>