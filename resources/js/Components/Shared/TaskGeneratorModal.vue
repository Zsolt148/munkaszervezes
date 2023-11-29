<template>
    <v-dialog v-model="isVisible" max-width="600" @click:outside="close">
        <v-card :loading="loading">
            <v-toolbar>
                <h3 class="modal--title">
                    Feladatgenerálás
                </h3>
                <v-spacer></v-spacer>
                <v-btn icon @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-toolbar>
            <div class="modal--body" v-if="tasks.length">

                <v-form v-model="valid" ref="form">

                    <v-tabs v-model="activeTab" class="mb-5">
                        <v-tab v-for="(task, key) in tasks" :key="key">
                            {{ task.properties.name.value }} ({{ task.count ? task.count : 0 }})
                        </v-tab>
                        <v-tab @click="addTask">
                            <v-icon>mdi-plus</v-icon>
                        </v-tab>
                    </v-tabs>

                    <v-tabs-items v-model="activeTab">
                        <v-tab-item v-for="(task, key) in tasks" :key="key">
                            <v-row class="pt-2">
                                <v-col cols="6">
                                    <v-text-field
                                        v-model="task.count"
                                        label="Feladat generálás darab"
                                        dense
                                        suffix="db"
                                        name="count"
                                        type="number"
                                        filled
                                        hide-details
                                    />
                                </v-col>

                                <v-col cols="6" v-for="(field, name) in task.properties">

                                    <v-text-field
                                        v-if="field.type == 'text'"
                                        v-model="field.value"
                                        :rules="field.required ? rules : []"
                                        :name="name"
                                        :label="trans(field.label)"
                                        :type="field.type"
                                        filled
                                        dense
                                        hide-details
                                    />

                                    <v-select
                                        v-else-if="field.type == 'select'"
                                        v-model="field.value"
                                        :rules="field.required ? rules : []"
                                        :name="name"
                                        :label="trans(field.label)"
                                        :items="field.items"
                                        clearable
                                        filled
                                        dense
                                        hide-details
                                    />

                                    <v-menu
                                        v-else-if="field.type === 'date'"
                                        :ref="`picker-${field.label}-${key}`"
                                        :close-on-content-click="false"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="field.value"
                                                :label="trans(field.label)"
                                                :rules="field.required ? rules : []"
                                                readonly
                                                dense
                                                hide-details="auto"
                                                filled
                                                v-bind="attrs"
                                                v-on="on"
                                            />
                                        </template>
                                        <v-date-picker
                                            v-model="field.value"
                                            min="1950-01-01"
                                            @change="save($refs[`picker-${field.label}-${key}`], field.value)"
                                        />
                                    </v-menu>
                                </v-col>
                            </v-row>
                        </v-tab-item>
                        <v-tab-item />
                    </v-tabs-items>

                    <div class="d-flex">
                        <v-btn
                            @click="close"
                            outlined
                            color="secondary">
                            {{ trans('Cancel') }}
                        </v-btn>
                        <v-spacer></v-spacer>
                        <v-btn
                            @click="submit"
                            :disabled="loading"
                            :loading="loading"
                            color="primary">
                            {{ trans('Create') }}
                        </v-btn>
                    </div>
                </v-form>
            </div>
        </v-card>
    </v-dialog>
</template>

<script>
import { flash } from "@/Use/helpers";

export default {
    props: {
        isVisible: Boolean,
    },
    created() {
        this.fetchTasks()
    },
    data() {
        return {
            activeTab: 0,
            loading: false,
            defaultTasks: [],
            empty_task: [],
            tasks: [],
            valid: false,
            menu: false,
            showErrorDeliveryMsg: false,
            errorDeliveryCount: null,
            rules: [
                v => !!v || this.trans('Required field')
            ]
        }
    },
    methods: {
        async fetchTasks() {
            this.loading = true
            await axios.get(this.route('tasks.task-generator.fetch-tasks'))
                .then(resp => {
                    this.empty_task = resp.data.empty_task
                    this.defaultTasks = JSON.parse(JSON.stringify(resp.data.tasks))
                    this.tasks = resp.data.tasks
                })
                .catch(err => {
                    console.error(err)
                })
            this.loading = false
        },
        save(ref, date) {
            ref[0].save(date);
        },
        addTask() {
            // clone empty task
            this.tasks.push(JSON.parse(JSON.stringify(this.empty_task)))

            this.$nextTick(() => this.activeTab = this.tasks.length)
        },
        async submit() {

            if (!this.$refs.form.validate()) {
                return;
            }

            this.loading = true

            const resp = await axios.post(this.route('tasks.task-generator.create-tasks'), {
                tasks: this.tasks,
            })

            flash(this, resp.data)
            this.loading = false
            this.close()
        },
        close() {
            this.tasks = JSON.parse(JSON.stringify(this.defaultTasks)) // reset
            this.activeTab = 0
            this.$emit('close')
        }
    },
}
</script>