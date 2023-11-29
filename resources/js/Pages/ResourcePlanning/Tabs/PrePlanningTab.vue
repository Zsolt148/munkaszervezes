<template>
    <section style="flex-direction:column" class="resource--planning">

        <v-row class="mb-0 pb-0">
            <v-col md="4">
                <view-picker :views="views" @change="updateViewPicker"/>
            </v-col>

            <v-col md="4">
                <v-select
                    :items="allRoles"
                    :placeholder="trans('Group')"
                    v-model="selectedRole"
                    @change="fetchData"
                    z-index="15"
                    clearable
                    hide-details="auto"
                    dense
                    filled/>
            </v-col>

            <v-col md="4">
                <v-select
                    :items="variants"
                    v-model="selectedVariant"
                    :placeholder="trans('Plan variant')"
                    @change="fetchTasks(); fetchData();"
                    item-text="name"
                    return-object
                    z-index="15"
                    hide-details="auto"
                    dense
                    filled/>
            </v-col>
        </v-row>

        <v-row class="mb-5">
            <v-col>
                <v-btn class="me-2" @click="createVariant" color="primary" :disabled="loading">
                    <v-icon class="pe-1">mdi-plus-circle-outline</v-icon>
                    {{ trans("Create variant") }}
                </v-btn>

                <v-btn class="me-2" v-if="selectedVariant" @click="importCurrentPlan" outlined color="primary"
                       :disabled="loading">
                    <v-icon class="pe-1">mdi-import</v-icon>
                    {{ trans("Import current plan") }}
                </v-btn>

                <v-btn class="me-2" v-if="selectedVariant" @click="editVariant" outlined color="primary"
                       :disabled="loading">
                    <v-icon class="pe-1">mdi-calendar-edit</v-icon>
                    {{ trans("Edit variant") }}
                </v-btn>

                <v-btn v-if="selectedVariant" @click="deleteVariant" outlined color="red" :disabled="loading">
                    <v-icon class="pe-1">mdi-delete-sweep-outline</v-icon>
                    {{ trans("Delete variant") }}
                </v-btn>
            </v-col>
        </v-row>

        <div class="d-flex" v-if="loading">
            <v-progress-circular indeterminate size="64" class="ma-auto"/>
        </div>
        <div class="d-flex mx-1" v-else-if="variants.length">
            <v-card elevation="1" class="task--selector">
                <v-card-title>
                    Feladatok ({{ filteredCompTasks.length }})
                </v-card-title>
                <v-card-text>
                    <v-text-field
                        v-model="search"
                        :label="trans('Search')"
                        class="mb-3"
                        filled
                        dense
                        hide-details="auto"
                        item-text="text"
                        item-value="order"
                        clearable
                    />
                    <draggable
                        v-if="filteredCompTasks.length"
                        v-model="filteredCompTasks"
                        v-bind="dragOptions"
                        class="dragArea list-group"
                    >
                        <pre-task-card
                            class="list-group-item"
                            v-for="task in filteredCompTasks"
                            v-show="task"
                            :key="task.task_id"
                            :task="task.task"
                        />
                    </draggable>
                </v-card-text>
            </v-card>

            <div class="worker--container w-full">
                <div class="dates--container">
                    <v-col v-if="dates[0] && dates[0].datePeriod" style="height:85px;" class="date-col first">
                        <div class="dates">
                            {{ dates[0].datePeriod }}
                        </div>
                    </v-col>

                    <v-col class="date-col" v-for="(date, index) in dates" :key="index">
                        <div class="dates" :data-date-row="`${index}-${selectedVariant.id}`">
                            <p>{{ date.day }}</p>
                            <small>{{ date.dateDay }}</small>
                        </div>
                    </v-col>
                </div>

                <pre-planning-card
                    v-show="selectedVariant"
                    v-for="role in roles"
                    @fetchData="fetchData"
                    @fetchTasks="fetchTasks"
                    :variant-id="selectedVariant ? selectedVariant.id : null"
                    :role="role"
                    :key="role.value"
                />
            </div>
        </div>

        <div class="w-full">
            <v-btn v-if="selectedVariant" @click="overrideCurrentPlan" color="primary" normal :disabled="loading"
                   class="d-block ms-auto my-5">
                {{ trans('Save planning') }}
            </v-btn>
        </div>

        <planning-modal
            @close="hidePlanningModal"
            @create="storeVariant"
            @update="updateVariant"
            :is-visible="isVisible"
            :form="form"
        />

        <confirms-modal v-bind="confirmProps" ref="confirm"/>
    </section>
</template>

<script>
import PreTaskCard from "@/Components/ResourcePlanning/Pre/PreTaskCard";
import PrePlanningCard from "@/Components/ResourcePlanning/Pre/PrePlanningCard";
import ViewPicker from "@/Components/Shared/ViewPicker";
import ConfirmsModal from "@/Components/Shared/ConfirmsModal.vue"
import PlanningModal from "../PlanningModal.vue"
import moment from "moment/moment";
import draggable from "vuedraggable";
import {flash} from "@/Use/helpers"

export default {
    name: 'PrePlanningTab',
    components: {
        draggable,
        PreTaskCard,
        PrePlanningCard,
        ViewPicker,
        PlanningModal,
        ConfirmsModal
    },

    props: ['views', 'refreshPending', 'allRoles'],

    data() {
        return {
            overlay: false,
            orderOptions: [
                {order: 'priority', text: this.trans('Order by priority')},
                {order: 'date', text: this.trans('Order by date')},
                {order: 'created_at', text: this.trans('Order by created at')},
            ],
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost",
                scrollSensitivity: 500,
                forceFallback: false
            },
            isVisible: false,
            search: '',
            selectedRole: null,
            filteredRoles: [],
            selectedVariant: null,
            compTasks: [],
            roles: [],
            dates: [],
            loading: true,
            actionTypeProps: {
                import: {
                    title: 'Import current plan',
                    content: 'Are you sure you want to import current plan?',
                    cancelText: 'Cancel',
                    button: 'Import current planning and override variant'
                },
                override: {
                    title: 'Save preplanning',
                    content: 'Are you sure you want to save the pre plan? The operation cannot be undone',
                    cancelText: 'Cancel',
                    button: 'Save planning and override current plan'
                },
                delete: {
                    title: 'Delete variant',
                    content: 'Are you sure you want to delete the pre plan? The operation cannot be undone',
                    cancelText: 'Cancel',
                    button: 'Delete variant'
                },
            },
            confirmProps: {
                title: '',
                cancelText: '',
                content: '',
                button: '',
            },
            selectedDay: null,
            selectedWeek: [
                moment().startOf('week').add(1, 'd').format('YYYY-MM-DD'),
                moment().endOf('week').add(1, 'd').format('YYYY-MM-DD')
            ],
            form: this.$inertia.form({
                id: '',
                name: '',
                isCreate: true,
                variant: null,
            }),
            selectedMonth: null,
            variants: []
        }
    },

    methods: {
        async fetchData() {
            await axios.get(this.route('plan-variants.fetch-data'), {
                params: {
                    day: this.selectedDay,
                    week: this.selectedWeek,
                    month: this.selectedMonth,
                    role: this.selectedRole,
                    variantId: this.selectedVariant ? this.selectedVariant.id : null,
                }
            }).then((resp) => {
                this.roles = resp.data.roles;
                this.dates = resp.data.dates;
            })
        },

        async fetchTasks() {
            await axios.get(this.route('plan-variants.fetch-tasks'), {
                params: {
                    day: this.selectedDay,
                    week: this.selectedWeek,
                    month: this.selectedMonth,
                    role: this.selectedRole,
                    variantId: this.selectedVariant ? this.selectedVariant.id : null,
                }
            }).then((resp) => {
                this.compTasks = resp.data.tasks;
                this.tags = resp.data.tags;
            })
        },

        async fetchVariants() {
            await axios.get(this.route('plan-variants.fetch-variants')).then((resp) => {
                this.variants = resp.data.variants
                if (this.variants.length) {
                    this.selectedVariant = this.variants[this.variants.length - 1];
                }
            });
        },

        async importCurrentPlan() {
            this.setConfirmProps('import');
            const confirmed = await this.$refs.confirm.open();
            if (!confirmed) return;

            try {
                const {data} = await axios.get(this.route('plan-variants.import-current'), {
                    params: {
                        variantId: this.selectedVariant?.id || null,
                    },
                });
                flash(this, data);
                await Promise.all([
                    this.fetchVariants(),
                    this.fetchTasks(),
                    this.fetchData(),
                ]);
            } catch (error) {
                console.error(error);
            }
        },

        updateViewPicker({day, week, month}) {
            this.selectedDay = day
            this.selectedWeek = week
            this.selectedMonth = month
            this.fetchData()
        },

        showPlanningModal() {
            this.isVisible = true;
        },

        createVariant() {
            this.form.name = '';
            this.form.id = '';
            this.form.isCreate = true;
            this.showPlanningModal();
        },

        async storeVariant() {
            this.loading = true
            await this.form.post(this.route('plan-variant.store'), {
                onSuccess: async () => {
                    this.resetForm();
                    this.hidePlanningModal();
                    await this.fetchVariants();
                    await this.fetchTasks();
                    await this.fetchData();
                    this.loading = false
                },
            })
        },

        editVariant() {
            this.form.name = this.selectedVariant.name;
            this.form.id = this.selectedVariant.id
            this.form.isCreate = false;
            this.showPlanningModal();
        },

        updateVariant() {
            this.form.patch(this.route('plan-variants.update', this.form.id), {
                onSuccess: () => {
                    this.selectedVariant.name = this.form.name;
                    this.hidePlanningModal();
                }
            })
        },

        async deleteVariant() {
            this.setConfirmProps('delete')
            if (await this.$refs.confirm.open()) {
                this.loading = true
                this.form.name = this.selectedVariant.name;
                this.form.id = this.selectedVariant.id
                this.form.delete(this.route('plan-variant.destroy', this.form.id), {
                    preserveScroll: true,
                    onSuccess: async () => {
                        this.selectedVariant = null
                        await this.fetchVariants();
                        await this.fetchTasks();
                        await this.fetchData();
                        this.loading = false
                    }
                })
            }
        },

        async overrideCurrentPlan() {
            this.setConfirmProps('override', this.task)
            if (await this.$refs.confirm.open()) {
                axios.patch(this.route('plan-variants.override', {variant: this.selectedVariant}), {}).then((resp) => {
                    flash(this, resp.data)
                    this.fetchVariants();
                    this.fetchTasks();
                    this.fetchData();
                }).catch(() => {
                    console.error('Unable to override')
                })
            }
        },

        setConfirmProps(type, item = null) {
            this.actionType = type;
            this.confirmProps = this.actionTypeProps[type] || {};
        },

        resetForm() {
            this.form.name = ''
            this.form.id = ''
            this.form.isCreate = true
        },

        hidePlanningModal() {
            this.isVisible = false;
        }
    },

    async created() {
        this.loading = true;
        await this.fetchVariants();
        if (this.selectedVariant) {
            await this.fetchTasks();
            await this.fetchData();
        }
        this.loading = false;
    },

    computed: {
        filteredCompTasks: {
            get() {
                let cards = Array.from(this.compTasks);
                let search = this.search;
                if (search) {
                    return cards.filter(card => card.task).filter(card => card.task.name.toLowerCase().indexOf(search) > -1);
                }

                return cards;
            },

            set() {

            }
        }
    },

    watch: {
        refreshPending(val) {
            if (val) {
                this.fetchTasks()
                this.fetchData()
                this.$emit('refreshed')
            }
        }
    }
}
</script>