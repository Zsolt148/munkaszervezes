<template>
    <v-row class="mb-5">
        <v-col cols="4">
            <v-autocomplete
                v-model="admin"
                label="Munkatárs"
                :loading="isLoading"
                :items="items"
                :placeholder="trans('Start typing to search')"
                :search-input.sync="search"
                item-text="name"
                item-value="id"
                clearable
                single-line
                return-object
                filled
                dense
                hide-details="auto"
                hide-no-data
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
        <v-col cols="4">
            <v-menu
                ref="menu2"
                z-index="15"
                v-model="menu2"
                :close-on-content-click="false"
                :return-value.sync="selectedDates"
                transition="scale-transition"
                close-on-click
                offset-y
                max-width="290px"
                min-width="auto"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="monthRangeText"
                        readonly
                        append-icon="mdi-calendar"
                        :label="trans('Date')"
                        hide-details="auto"
                        single-line
                        dense
                        filled
                        v-bind="attrs"
                        v-on="on"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="selectedDates"
                    show-current
                    show-adjacent-months
                    first-day-of-week="1"
                    range
                    no-title
                    scrollable
                    :min="minMonth"
                    :max="maxMonth"
                    locale="hu-hu"
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
                        @click="saveMonth"
                    >
                        OK
                    </v-btn>
                </v-date-picker>
            </v-menu>
        </v-col>
    </v-row>
</template>

<script>
import moment from "moment/moment";

export default {
    name: "HistoryFilter",
    data() {
        return {
            menu2: null,
            items: [],
            admin: null,
            isLoading: false,
            search: null,
            timeout: null,
            minMonth: null,
            maxMonth: null,
            selectedDates: [],
        }
    },
    methods: {
        saveMonth() {
            this.$refs.menu2.save(this.selectedDates)
            this.change()
        },
        change() {
            this.$emit('change', {
                adminId: this.admin ? this.admin.id : null,
                dates: this.selectedDates
            })
        }
    },
    watch: {
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
        admin(val) {
            this.change()
        },
        selectedDates(val) {
            if (val && val.length == 1) {
                this.minMonth = moment(val[0]).subtract(1, 'months').format('YYYY-MM-DD')
                this.maxMonth = moment(val[0]).add(1, 'months').format('YYYY-MM-DD')
            }else {
                this.minMonth = null
                this.maxMonth = null
            }
        },
    },
    computed: {
        monthRangeText () {
            if (!this.selectedDates.length) {
                return ''
            }
            const start = moment(this.selectedDates[0]).format('MM-DD');
            const end = moment(this.selectedDates[1]).format('MM-DD');

            return start + ' ~ ' + end;
        },
    }
}
</script>

<style scoped>

</style>