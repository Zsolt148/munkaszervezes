<template>
    <v-fade-transition leave-absolute>
        <div class="d-flex">
            <div class="" :class="{ 'ms-0': role.parent_id != null }" >
                <div class="role--card" :class="{ 'child': role.parent_id != null }" :key="role.value">
                    <div class="role--card-header">
                        <h5>{{ role.text }}</h5>
                        <p>{{ role.task_count }} kiosztásra váró feladat</p>
                    </div>
                    
                    <div class="d-flex">
                        <div v-for="(user, index) in role.users" class="role--card-boss">
                            <v-tooltip v-if="index === 0" bottom>
                                <template v-slot:activator="{ on }">
                                    <v-avatar v-on="on" :style="{ 'border': '1.5px solid white' }" size="30">
                                        <v-img :src="getProfPicture(user.photo, user.name)"></v-img>
                                    </v-avatar>
                                </template>
                                {{ user.name }}
                            </v-tooltip>
                            <v-tooltip v-else bottom>
                                <template v-slot:activator="{ on }">
                                    <v-avatar v-on="on" :style="{ 'border': '1.5px solid white', 'margin-left': '-10px' }" size="30">
                                        <v-img :src="getProfPicture(user.photo, user.name)"></v-img>
                                    </v-avatar>
                                </template>
                                {{ user.name }}
                            </v-tooltip>
                        </div>
                    </div>

                    <div class="task--selector-body mt-4">
                        <draggable
                            v-model="role.tasks"
                            :options="dragOptions"
                            class="dragArea list-group"
                            @start="isDragging = true"
                            @end="isDragging = false"
                            @change="onTaskChange($event, role.value)"
                            >
                            <task-card
                                @fetchData="$emit('fetchData')"
                                class="list-group-item"
                                v-for="task in role.tasks"
                                v-show="task"
                                :key="task.id"
                                :task="task"
                            />
                        </draggable>
                    </div>

                    <div v-show="role.children && role.children.length > 0">
                        <v-btn
                            class="roles--card-button"
                            color="primary"
                            small
                            ripple
                            outlined
                            @click="toggleChildren"
                            >
                            {{ !showChildren ? 'Alcsoportok megjelenítése' : 'Alcsoportok elrejtése' }}
                            <v-icon class="ms-2">
                                {{ showChildren ? 'mdi-arrow-down-thin' : 'mdi-arrow-right-thin' }}
                            </v-icon>
                        </v-btn>
                    </div>
                </div>
            </div>

            <v-fade-transition leave-absolute>
                <div class="d-flex" v-if="showChildren">
                    <role-card @fetchData="$emit('fetchData')" v-for="child in role.children" :role="child" :key="child.value" />
                </div>
            </v-fade-transition>

        </div>
    </v-fade-transition>
</template>

<script>
import TaskCard from "@/Components/ResourcePlanning/TaskCard";
import draggable from "vuedraggable";
import { flash } from "@/Use/helpers"

export default{
    name: "RoleCard",
    components:{
        draggable,
        TaskCard,
    },
    props:[
        'role'
    ],
    data(){
        return{
            showChildren: false,
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost",
                scrollSensitivity: 500,
                forceFallback: false
            },
        }
    },
    methods: {
        onTaskChange(event, id) {
            if (event && event.hasOwnProperty('added')) {
                this.updateStatus(event.added.element, id);
            }
        },

        toggleChildren() {
            this.showChildren = !this.showChildren;
        },

        async updateStatus(task, roleId) {
            await axios.patch(this.route('resource-planning.update', { task: task.id }), {
                roleId: roleId
            }).then((resp) => {
                this.$emit('fetchData')
                flash(this, resp.data)
            })
        },

        fetchData(){
            this.$emit('fetchData');
        }
    },
    watch: {
        isDragging(newValue) {
            if (newValue) {
                this.delayedDragging = true;
                return;
            }
            this.$nextTick(() => {
                this.delayedDragging = false;
            });
        }
    },
}
</script>