<template>
    <div>
        <v-menu z-index="15" v-model="menu" offset-x right v-if="isTask">
            <template v-slot:activator="{ on }">
                <v-card outlined ripple class="rp--card handle mx-auto list-group-item mb-2">
                    <v-card-text class="ma-0 pa-0" v-on="on">
                        <div class="rp--card-data">
                            <div class="rp--card-name">
                                {{ `${task.name.substring(0, 32)}...` }}
                            </div>

                            <div>
                                <!--<v-chip small v-for="tag in task.tags">
                                    {{ tag }}
                                </v-chip>
                                -->
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
                        {{ task.type_text }}
                    </div>
                </div>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
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
        
    },
    computed: {
        isTask() {
            return this.task.name;
        }
    }
}
</script>