<template>
    <v-row>
        <v-col>
            <v-select
                :items="views"
                v-model="selectedView"
                :placeholder="trans('View')"
                hide-details="auto"
                filled
                dense
            />
        </v-col>
        <v-col v-show="selectedView == 'daily'">
            <v-menu
                ref="menu1"
                z-index="15"
                v-model="menu1"
                :close-on-content-click="false"
                :return-value.sync="selectedDay"
                transition="scale-transition"
                offset-y
                min-width="auto"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="selectedDay"
                        readonly
                        append-icon="mdi-calendar"
                        dense
                        hide-details="auto"
                        filled
                        v-bind="attrs"
                        v-on="on"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="selectedDay"
                    no-title
                    scrollable
                    first-day-of-week="1"
                    locale="hu-hu"
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
                        @click="saveDay"
                    >
                        OK
                    </v-btn>
                </v-date-picker>
            </v-menu>
        </v-col>

        <v-col v-show="selectedView == 'monthly'">
            <v-menu
                ref="menu2"
                z-index="15"
                v-model="menu2"
                :close-on-content-click="false"
                :return-value.sync="selectedMonth"
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
                        hide-details="auto"
                        dense
                        filled
                        v-bind="attrs"
                        v-on="on"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="selectedMonth"
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
        <v-col v-show="selectedView == 'weekly'">
            <v-menu
                ref="menu3"
                z-index="15"
                v-model="menu3"
                :close-on-content-click="false"
                :return-value.sync="selectedWeek"
                transition="scale-transition"
                offset-y
                min-width="auto"
            >
                <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                        v-model="weekRangeText"
                        readonly
                        append-icon="mdi-calendar"
                        dense
                        filled
                        hide-details="auto"
                        v-bind="attrs"
                        v-on="on"
                    ></v-text-field>
                </template>
                <v-date-picker
                    v-model="selectedWeek"
                    show-current
                    first-day-of-week="1"
                    show-adjacent-months
                    range
                    no-title
                    scrollable
                    :min="minWeek"
                    :max="maxWeek"
                    locale="hu-hu"
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
                        @click="saveWeek"
                    >
                        OK
                    </v-btn>
                </v-date-picker>
            </v-menu>
        </v-col>
    </v-row>
</template>

<script>
import moment from 'moment'

export default {
    name: "ViewPicker",
    props: ['views'],
    data() {
        return {
            selectedView: 'monthly', // default
            menu1: null,
            menu2: null,
            menu3: null,
            minWeek: null,
            maxWeek: null,
            minMonth: null,
            maxMonth: null,
            selectedDay: null,
            selectedMonth: [
                moment().startOf('month').format('YYYY-MM-DD'),
                moment().endOf('month').format('YYYY-MM-DD')
            ],
            selectedWeek: [
                // moment().startOf('week').add(1, 'd').format('YYYY-MM-DD'),
                // moment().endOf('week').add(1, 'd').format('YYYY-MM-DD')
            ]
        }
    },
    methods: {
        saveDay() {
            this.$refs.menu1.save(this.selectedDay)
            this.change()
        },
        saveWeek() {
            this.$refs.menu3.save(this.selectedWeek)
            this.change()
        },
        saveMonth() {
            this.$refs.menu2.save(this.selectedMonth)
            this.change()
        },
        change() {
            this.$emit('change', {
                day: this.selectedDay,
                week: this.selectedWeek,
                month: this.selectedMonth
            })
        }
    },
    watch: {
        selectedWeek(val) {
            if (val && val.length == 1) {
                this.minWeek = moment(val[0]).subtract(7, 'days').format('YYYY-MM-DD')
                this.maxWeek = moment(val[0]).add(7, 'days').format('YYYY-MM-DD')
            }else {
                this.minWeek = null
                this.maxWeek = null
            }
        },
        selectedMonth(val) {
            if (val && val.length == 1) {
                this.minMonth = moment(val[0]).subtract(1, 'months').format('YYYY-MM-DD')
                this.maxMonth = moment(val[0]).add(1, 'months').format('YYYY-MM-DD')
            }else {
                this.minMonth = null
                this.maxMonth = null
            }
        },
        selectedView(val) {
            this.selectedDay = null
            this.selectedMonth = null
            this.selectedWeek = []

            if (val == 'daily') {
                this.selectedDay = (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substring(0, 10)
                this.saveDay()
            }else if (val == 'monthly') {
                this.selectedMonth = [
                    moment().startOf('month').format('YYYY-MM-DD'),
                    moment().endOf('month').format('YYYY-MM-DD')
                ]
                this.saveMonth()
            }else if(val == 'weekly') {
                this.selectedWeek = [
                    moment().startOf('week').add(1, 'd').format('YYYY-MM-DD'),
                    moment().endOf('week').add(1, 'd').format('YYYY-MM-DD')
                ]
                this.saveWeek()
            }
        }
    },
    computed: {
        weekRangeText () {
            if (!this.selectedWeek) {
                return ''
            }
            const start = moment(this.selectedWeek[0]).format('MM-DD');
            const end = moment(this.selectedWeek[1]).format('MM-DD');

            return start + ' ~ ' + end;
        },
        monthRangeText () {
            if (!this.selectedMonth) {
                return ''
            }
            const start = moment(this.selectedMonth[0]).format('MM-DD');
            const end = moment(this.selectedMonth[1]).format('MM-DD');

            return start + ' ~ ' + end;
        },
    }
}
</script>

<style scoped>

</style>