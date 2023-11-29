<template>
    <v-dialog v-model="isVisible" max-width="577px" persistent @click:outside="close">
        <v-card>

            <v-card-title class="dialog--title">
                {{ trans('Record time') }}

                <v-btn class="ms-auto" icon light @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-row>
                    <v-col>
                        <b class="modal--bold-text">#{{ task.id }} {{ trans('Task to spend time on') }} </b>
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            :label="trans('Task')"
                            :value="task.name"
                            filled
                            disabled
                            hide-details="auto" />
                    </v-col>

                    <v-col cols="12">
                        <v-menu v-model="menu" :close-on-content-click="false" ref="menu" transition="scale-transition"
                            offset-y max-width="290px" min-width="auto">
                            <template v-slot:activator="{ on, attrs }">
                                <v-text-field
                                    v-model="form.date"
                                    :error-messages="form.errors ? form.errors['date'] : null"
                                    :label="trans('Date')"
                                    prepend-inner-icon="mdi-calendar"
                                    readonly
                                    hide-details="auto"
                                    filled
                                    v-bind="attrs" v-on="on" />
                            </template>
                            <v-date-picker
                                v-model="form.date"
                                no-title
                                first-day-of-week="1"
                                locale="hu-HU"
                                @input="menu = false" />
                        </v-menu>
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            v-model="form.time"
                            :label="trans('Hour count')"
                            :error-messages="form.errors ? form.errors['time'] : null"
                            filled
                            type="number"
                            required
                            hide-details="auto" />
                    </v-col>

                    <v-col cols="12">
                        <v-text-field
                            v-model="form.description"
                            :label="trans('Description')"
                            :error-messages="form.errors ? form.errors['description'] : null"
                            filled
                            height="100"
                            required
                            hide-details="auto" />
                    </v-col>
                </v-row>
            </v-card-text>

            <v-card-actions class="px-5 pb-6 d-flex justify-space-between">
                <v-btn
                    class="text-none button"
                    outlined
                    color="secondary"
                    type="button"
                    @click="close">
                    {{ trans('Cancel') }}
                </v-btn>
                <v-btn
                    @click="submit"
                    type="submit"
                    color="primary"
                >
                    {{ trans('Save') }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: 'TaskMeasureModal',
    props: {
        task: {
            type: Object,
            required: true
        },
        isVisible: {
            type: Boolean,
            required: true
        }
    },
    data() {
        return {
            activePicker: null,
            menu: false,
            form: this.$inertia.form({
                time: '',
                date: '',
                description: ''
            }),
        }
    },
    methods: {
        clearForm() {
            this.form = this.$inertia.form({
                time: "",
                date: "",
                description: ''
            })
        },
        save(date) {
            this.$refs.menu.save(date)
        },
        close() {
            this.$emit("close");
        },
        submit() {
            this.form.post(this.route('tasks.log-time', { taskId: this.task.id }), {
                onSuccess: () => {
                    this.clearForm();
                    this.close();
                }
            })
        },
    },
    computed: {
        today() {
            return new Date().toISOString().substr(0, 10) // format the date as YYYY-MM-DD
        }
    }
}
</script>