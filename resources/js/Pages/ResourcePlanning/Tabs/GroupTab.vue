<template>
    <section class="resource--planning mx-1">
        <v-progress-circular indeterminate size="64" v-if="loading" class="ma-auto" />
        <v-card elevation="0" class="task--selector" outlined v-else>
            <v-card-title>
                Feladatok ({{filteredCompTasks.length}})
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
                <div class="task--selector-body py-2 px-2">
                    <draggable
                        v-if="filteredCompTasks.length"
                        v-model="filteredCompTasks"
                        v-bind="dragOptions"
                        class="dragArea list-group">
                        <task-card
                            class="list-group-item"
                            v-for="task in filteredCompTasks"
                            v-show="task"
                            :key="task.id"
                            :task="task"
                            @click="click(task)"
                        />
                    </draggable>
                </div>
            </v-card-text>
        </v-card>

        <div class="roles--container" v-if="!loading">
            <role-card
                v-for="role in roleTasks"
                @fetchData="fetchData"
                :role="role"
                :key="role.value"/>
        </div>
    </section>
</template>

<script>
import draggable from "vuedraggable";
import TaskCard from "@/Components/ResourcePlanning/TaskCard";
import RoleCard from "@/Components/ResourcePlanning/RoleCard.vue"

export default {
    name: 'GroupTab',
    components: {
        draggable,
        TaskCard,
        RoleCard
    },
    props: ['refreshPending'],
    data() {
        return {
            orderOptions: [
                {
                    order: 'priority',
                    text: this.trans('Order by priority')
                },
                {
                    order: 'deadline',
                    text: this.trans('Order by deadline')
                },
                {
                    order: 'created_at',
                    text: this.trans('Order by created at')
                }
            ],
            loading: true,
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost",
                scrollSensitivity: 500,
                forceFallback: false
            },
            search: '',
            compTasks: [],
            roleTasks: [],
        }
    },
    methods: {
        async fetchData() {
            await axios.get(this.route('resource-planning.fetch-group')).then((resp) => {
                this.tags = resp.data.tags,
                this.compTasks = resp.data.tasks
                this.roleTasks = resp.data.roles
            })
        },
    },

    async created() {
        this.loading = true
        await this.fetchData();
        this.loading = false
    },

    computed: {
        filteredCompTasks: {
            get() {
                let cards = Array.from(this.compTasks);
                let search = this.search;

                if (search) {
                    return cards.filter(card => card.name.toLowerCase().indexOf(search) > -1);
                }

                return cards;
            },

            set(){

            }
        }
    },

    watch: {
        refreshPending(val) {
            if(val) {
                this.fetchData()
                this.$emit('refreshed')
            }
        }
    }
}
</script>