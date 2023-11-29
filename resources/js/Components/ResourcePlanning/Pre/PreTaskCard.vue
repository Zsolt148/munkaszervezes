<template>
    <div>
        <v-menu z-index="15" v-model="menu" offset-x right v-if="isTask">
            <template v-slot:activator="{ on }">
                <v-card outlined ripple class="rp--card handle mx-auto list-group-item mb-2">
                    <v-card-text class="ma-0 pa-0" v-on="on">
                        <div class="rp--card-data">
                            <span class="rp--card-id">#{{ task.id }} - {{ task.park ? task.park.name : '' }}
                                <v-icon v-if="taskId" class="ms-auto" @click="removeTask()">
                                    mdi-delete
                                </v-icon>
                            </span>

                            <div class="rp--card-name">
                                {{ `${task.name.substring(0, 32)}...` }}
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
            </template>

            <canban-card :task="task" />
        </v-menu>
        <v-card outlined ripple class="rp--card-leave mx-auto list-group-item mb-2" v-else>
            <v-card-text class="ma-0 pa-0">
                <div class="rp--card-data">
                    <div class="rp--card-leave-name">
                        {{ task ? task.type_text : '' }}
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import { flash } from "@/Use/helpers"
import CanbanCard from "@/Components/ResourcePlanning/CanbanCard";

export default {
    name: "PreTaskCard",
    props: ['task', 'variantId', 'taskId'],
    data() {
        return {
            menu: false
        }
    },
    components:{
        CanbanCard
    },  
    methods: {
        async removeTask() {
            const resp = await axios.patch(this.route('plan-variants.remove', { task: this.taskId, variant: this.variantId}));
            this.$emit('fetchData');
            this.$emit('fetchTasks');
            flash(this, resp.data)
        },
    },
    computed: {
        isTask() {
            return this.task && this.task.name;
        }
    }
}
</script>