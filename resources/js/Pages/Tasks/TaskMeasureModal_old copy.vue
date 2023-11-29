<template>
    <v-dialog v-model="isVisible" max-width="800px" persistent @click:outside="close">
        <v-card>
            <v-card-title class="py-0">
                <v-container>
                    <v-row class="d-flex align-center">
                        <v-col class="ps-0">
                            <v-icon class="pe-1">mdi-clock-outline</v-icon> {{ trans('Log time') }} {{ taskId }}
                        </v-col>
                        <v-col>
                            <v-btn class="d-block ms-auto" icon light @click="close">
                                <v-icon>mdi-close</v-icon>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-container>
            </v-card-title>
            
            <v-divider/>

            <v-card-text class="mt-5 pt-5">
                <v-row>
                    <v-col cols="12" md="7">
                        <v-row>
                            <v-col cols="12" md="6">
                                <v-text-field
                                    v-model="form.time"
                                    :label="trans('Duration')"
                                    :error-messages="form.errors ? form.errors['time'] : null"
                                    filled
                                    dense
                                    type="number"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-clock-time-five-outline"
                                />
                            </v-col>

                            <v-col cols="12" md="6">
                                <v-menu
                                    v-model="menu"
                                    :close-on-content-click="false"
                                    ref="menu"
                                    transition="scale-transition"
                                    offset-y
                                    max-width="290px"
                                    min-width="auto"
                                >
                                    <template v-slot:activator="{ on, attrs }">
                                        <v-text-field
                                            v-model="form.date"
                                            :error-messages="form.errors ? form.errors['date'] : null"
                                            :label="trans('Date')"
                                            prepend-icon="mdi-calendar"
                                            hide-details="auto"
                                            filled
                                            dense
                                            v-bind="attrs"
                                            v-on="on">
                                        </v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="form.date"
                                        no-title
                                        @input="menu = false">
                                    </v-date-picker>
                                </v-menu>
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col>
                                <v-text-field
                                    v-model="form.description"
                                    :label="trans('Description')"
                                    :error-messages="form.errors ? form.errors['description'] : null"
                                    filled
                                    dense
                                    type="text"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-image-text"
                                />
                            </v-col>
                        </v-row>

                        <v-row>
                            <v-col cols="8">
                                <v-text-field
                                    v-model="remaningTime"
                                    :label="trans('Remaining estimate')"
                                    disabled
                                    filled
                                    dense
                                    type="number"
                                    required
                                    hide-details="auto"
                                    prepend-icon="mdi-chart-timeline-variant"
                                />
                            </v-col>

                            <v-col class="d-flex align-center" cols="4">
                                <small class="mb-0">{{ trans('Original estimate') }} {{ ogEstimated }}h</small>
                            </v-col>
                        </v-row>
                    </v-col>

                    <v-col cols="12" md="5">
                        <v-card v-for="wh in workingHours" outlined class="pa-2 mb-2">
                            {{ wh.description }} - {{ wh.time }}(h) - {{ wh.date }}
                        </v-card>
                    </v-col>
                </v-row>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="submit"
                        type="submit"
                        class="ms-5"
                        color="primary"
                        
                    >
                        {{ trans('Log time') }}
                    </v-btn>
                </v-card-actions>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import { EventBus } from '@/Components/Shared/eventbus'

export default{
    name: 'TaskMeasureModal',
    props: ['taskId', 'isVisible', 'workingHours', 'ogEstimated'],
    data(){
        return {
            activePicker: null,
            menu: false,
            form: this.$inertia.form({
                remaning_estimate: '5',
                time: "",
                date: "",
                description: ''
            }),
        }
    },
    methods:{
        clearForm() {
            this.form = this.$inertia.form({
                remaning_estimate: '',
                time: "",
                date: "",
                description: ''
            })
        },
        save (date) {
            this.$refs.menu.save(date)
        },
        close() {
            this.$emit("close");
        },
        submit() {
            this.form.post(this.route('tasks.log-time', { taskId: this.taskId }),{
                onSuccess: () => {
                    this.workingHours.push(this.form)        
                    EventBus.$emit('workingHoursUpdated', this.workingHours);
                    this.clearForm();
                    this.close();
                }
            })
        },
    },
    computed:{
        remaningTime(){
            let remaining = this.ogEstimated;
            this.workingHours.forEach(element => {
                remaining -= element.time;
            });
            return remaining;
        }
    }
}
</script>