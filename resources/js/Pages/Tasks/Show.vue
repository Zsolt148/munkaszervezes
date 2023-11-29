<template>
    <app-layout>
        <app-head :title="task.name"/>

        <v-col class="page--header pb-0">

            <Link :href="this.backUrl">
                {{ trans(this.backText) }}
            </Link>

            <div class="d-flex justify-space-between">
                <h1 class="mt-2">
                    {{ task.name }}
                </h1>

                <div>
                    <v-btn color="primary" @click="$inertia.visit(route('tasks.edit', task.id))" class="me-2" small>
                        <v-icon class="pe-2">mdi-pencil-outline</v-icon>
                        {{ trans('Edit') }}
                    </v-btn>

                    <v-btn
                        v-if="! task.is_watching"
                        color="secondary" @click="watchTask(task)" class="me-2" small>
                        <v-icon class="pe-2">mdi-eye</v-icon>
                        {{ trans('Watch') }}
                    </v-btn>

                    <v-btn
                        v-else-if="task.is_watching"
                        color="secondary" @click="unwatchTask(task)" class="me-2" small>
                        <v-icon class="pe-2">mdi-eye</v-icon>
                        {{ trans('Unwatch') }}
                    </v-btn>

                    <v-btn
                        v-if="!task.trashed"
                        :loading="restoreOrDeleteForm.processing"
                        @click="deleteTask"
                        color="red"
                        small
                        outlined
                    >
                        <v-icon class="pe-2">mdi-trash-can-outline</v-icon>
                        {{ trans('Delete') }}
                    </v-btn>

                    <v-btn
                        v-if="task.trashed"
                        filled
                        outlined
                        @click="restoreTask"
                        :loading="restoreOrDeleteForm.processing"
                    >
                        <v-icon class="pe-2">mdi-restore</v-icon>
                        {{ trans('Restore') }}
                    </v-btn>
                </div>
            </div>
        </v-col>

        <v-col>
            <div class="my-3 d-flex align-center">
                <v-btn @click="refreshPage" small outlined>
                    <v-icon>
                        mdi-refresh
                    </v-icon>
                </v-btn>

                <span class="ms-2">{{ trans('Updated at') }} {{ task.updated_at }}</span>
            </div>

            <div class="my-5">
                <b>{{ task.created_by.name }}</b> {{ trans('created this task') }} <b>{{ task.created_at }}</b>
            </div>

            <task-alert @recordTime="recordTime" :task="task"/>

            <v-row class="mt-3">
                <v-col cols="12" md="7">
                    <task-data-card :task="task"/>
                    <task-tags-card :task="task"/>
                    <task-description-card :task="task"/>

                    <v-tabs v-model="tab" class="my-5">
                        <v-tab>
                            {{ trans('History') }}
                        </v-tab>

                        <v-tab>
                            {{ trans('Comments') }}
                        </v-tab>
                    </v-tabs>

                    <v-tabs-items v-model="tab">
                        <v-tab-item>
                            <task-history :task="task"/>
                        </v-tab-item>
                        <v-tab-item>
                            <task-comments :task="task"/>
                        </v-tab-item>
                    </v-tabs-items>
                </v-col>

                <v-col cols="12" md="5">
                    <task-participant-card :task="task"/>
                    <task-location-card :task="task"/>
                </v-col>
            </v-row>
        </v-col>

        <task-measure-modal @close="closeRecordTimeModal" :task="task" :isVisible="isModalVisible"/>
        <confirms-modal v-bind="confirmProps" ref="confirm">
            <template v-slot:header>
                <div class="dialog--card">
                    <p>{{ trans('Task') }} # {{ task.id }}</p>
                    <h3>{{ task.name }}</h3>
                    <p>
                        {{ task.taskTypeText }}
                    </p>
                </div>
            </template>
        </confirms-modal>
    </app-layout>
</template>

<script>
import AppLayout from "@/Layouts/AppLayout";
import TaskDataCard from "@/Components/Tasks/TaskDataCard";
import TaskParticipantCard from "@/Components/Tasks/TaskParticipantCard";
import TaskLocationCard from "@/Components/Tasks/TaskLocationCard";
import TaskTagsCard from "@/Components/Tasks/TaskTagsCard";
import TaskDescriptionCard from "@/Components/Tasks/TaskDescriptionCard";
import TaskAlert from "@/Components/Tasks/TaskAlert";
import TaskHistory from "@/Components/Tasks/TaskHistory"
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";
import TaskMeasureModal from "./TaskMeasureModal.vue";
import TaskComments from "@/Pages/Tasks/TaskComments";

export default {
    name: "TaskShow",
    components: {
        AppLayout,
        TaskDataCard,
        TaskParticipantCard,
        TaskLocationCard,
        TaskTagsCard,
        TaskDescriptionCard,
        TaskAlert,
        TaskHistory,
        TaskComments,
        ConfirmsModal,
        TaskMeasureModal
    },
    props: {
        task: {
            type: Object,
            required: true,
        },
        backUrl: {
            type: String,
            default: route('tasks.index'),
            required: false,
        },
        backText: {
            type: String,
            default: "Back to tasks",
            required: false
        }
    },
    data() {
        return {
            tab: null,
            actionTypeProps: {
                restore: {
                    title: 'Restore task',
                    content: 'Are you sure you want to restore this task?',
                    cancelText: 'Cancel',
                    button: 'Restore task'
                },
                delete: {
                    title: 'Delete task',
                    content: 'Are you sure you want to delete this task?',
                    cancelText: 'Keep task',
                    button: 'Delete task'
                }
            },
            confirmProps: {
                title: '',
                cancelText: '',
                content: '',
                button: '',
            },
            watchForm: this.$inertia.form({}),
            restoreOrDeleteForm: this.$inertia.form({
                id: null
            }),
            isModalVisible: false
        }
    },
    methods: {
        refreshPage() {
            window.location.reload();
        },

        async deleteTask() {
            this.setConfirmProps('delete', this.task)
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.delete(this.route('tasks.destroy', {task: this.task.id}))
            }
        },

        async forceDeleteTask() {
            this.setConfirmProps('delete', this.task)
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.id = this.task.id
                this.restoreOrDeleteForm.delete(this.route("tasks.force-delete", this.task.id));
            }
        },

        async restoreTask() {
            this.setConfirmProps('restore', this.task)
            if (await this.$refs.confirm.open()) {
                this.restoreOrDeleteForm.id = this.task.id
                this.restoreOrDeleteForm.patch(this.route("tasks.restore", this.task.id));
            }
        },

        watchTask(task) {
            this.watchForm.post(this.route('tasks.watch-task', task.id));
        },

        unwatchTask(task) {
            this.watchForm.post(this.route('tasks.unwatch-task', task.id));
        },

        setConfirmProps(type, item = null) {
            this.actionType = type;
            this.selectedClient = item;
            this.confirmProps = this.actionTypeProps[type] || {};
        },

        recordTime() {
            this.isModalVisible = true;
        },

        closeRecordTimeModal() {
            this.isModalVisible = false;
        }
    }

};
</script>
