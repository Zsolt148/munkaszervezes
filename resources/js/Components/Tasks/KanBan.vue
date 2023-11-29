<template>
    <v-row class="mt-4 ma-0" v-if="!loading">
        <v-col class="canban--container pl-0">
            <h3 class="canban--container-header todo mb-4">{{trans('To do')}} {{ todo.length ? `(${todo.length})` : '' }}</h3>
            <draggable
                v-model="todo"
                v-bind="dragOptions"
                class="dragArea list-group"
                @start="isDragging = true"
                @end="isDragging = false"
                @change="onTodoChange"
            >
                <task-card 
                    v-for="task in todo"
                    v-show="task"
                    class="list-group-item"
                    :key="task.id"
                    :task="task"
                    @click="click(task)"
                />
            </draggable>
        </v-col>
        <v-col class="canban--container">
            <h3 class="canban--container-header in-progress mb-4">{{trans('In progress')}} {{ inprogress.length ? `(${inprogress.length})` : '' }}</h3>
            <draggable
                v-model="inprogress"
                v-bind="dragOptions"
                class="dragArea list-group"
                @start="isDragging = true"
                @end="isDragging = false"
                @change="onInprogressChange"
            >
                <task-card 
                    v-for="task in inprogress"
                    :key="task.id"
                    :task="task"
                    @click="click(task)"
                />
            </draggable>
        </v-col>
        <v-col class="canban--container">
            <h3 class="canban--container-header waiting mb-4">{{trans('Waiting')}} {{ waiting.length ? `(${waiting.length})` : '' }}</h3>
            <draggable
                v-model="waiting"
                v-bind="dragOptions"
                class="dragArea list-group"
                @start="isDragging = true"
                @end="isDragging = false"
                @change="onWaitingChange"
            >
                <task-card 
                    v-for="task in waiting"
                    :key="task.id"
                    :task="task"
                    @click="click(task)"
                />
            </draggable>
        </v-col>
        <v-col class="canban--container">
            <h3 class="canban--container-header waiting-for-repair mb-4">{{trans('Waiting for repair')}} {{ waiting_for_repair.length ? `(${waiting_for_repair.length})` : '' }}</h3>
            <draggable
                v-model="waiting_for_repair"
                v-bind="dragOptions"
                class="dragArea list-group"
                @start="isDragging = true"
                @end="isDragging = false"
                @change="onWaitingForRepairChange"
            >
                <task-card 
                    v-for="task in waiting_for_repair"
                    :key="task.id"
                    :task="task"
                    @click="click(task)"
                />
            </draggable>
        </v-col>
        <v-col class="canban--container pr-0">
            <h3 class="canban--container-header done mb-4">{{trans('Done')}} {{ done.length ? `(${done.length})` : '' }}</h3>
            <draggable
                v-model="done"
                v-bind="dragOptions"
                class="dragArea list-group"
                @start="isDragging = true"
                @end="isDragging = false"
                @change="onDoneChange"
            >
                <task-card 
                    v-for="task in done"
                    :key="task.id"
                    :task="task"
                    @click="click(task)"
                />
            </draggable>
        </v-col>
    </v-row>
</template>

<script>
import draggable from "vuedraggable";
import TaskCard from "@/Components/Tasks/TaskCard";
import { flash } from "@/Use/helpers"

export default {
    name: "KanBan",
    components: {
        draggable,
        TaskCard
    },

    props: {
        tasks: { type: Array },
        loading: { type: Boolean, default: false },
    },

    data() {
        return {
            todo: [],
            inprogress: [],
            waiting: [],
            waiting_for_repair: [],
            done: [],
            isDragging: false,
            delayedDragging: false,
            dragOptions: {
                animation: 120,
                group: "tasks",
                ghostClass: "ghost"
            },
        }
    },

    methods: {
        taskByStatus(status) {
            return this.tasks.filter(task => task.status == status)
        },

        onTodoChange({ added }) {
            if (added) {
                this.updateStatus(added.element, 'todo')
            }
        },

        onInprogressChange({ added }) {
            if (added) {
                this.updateStatus(added.element, 'in_progress')
            }
        },

        onWaitingChange({ added }) {
            if (added) {
                this.updateStatus(added.element, 'waiting')
            }
        },

        onWaitingForRepairChange({ added }) {
            if (added) {
                this.updateStatus(added.element, 'waiting_for_repair')
            }
        },

        onDoneChange({ added }) {
            if (added) {
                this.updateStatus(added.element, 'done')
            }
        },

        async updateStatus(task, status) {
            const resp = await axios.patch(this.route('tasks.kanban.update', { task: task.id }), {
                status: status
            })
            task.status = status
            task.statusText = resp.data.statusText;
            flash(this, resp.data)
        },

        click(task) {
            this.$emit('click', task)
        },
    },

    watch: {
        tasks(val) {
            if (val) {
                this.todo = this.taskByStatus('todo')
                this.inprogress = this.taskByStatus('in_progress')
                this.waiting = this.taskByStatus('waiting')
                this.waiting_for_repair = this.taskByStatus('waiting_for_repair')
                this.done = this.taskByStatus('done')
            }
        },
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

<style scoped>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}

.list-group {
    min-height: 200px;
    height: 100%;
}

.list-group-item {
    cursor: move;
}

.list-group-item i {
    cursor: pointer;
}
</style>