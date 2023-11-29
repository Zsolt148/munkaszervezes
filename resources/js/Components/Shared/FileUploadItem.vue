<template>
    <v-list-item class="uploading-video">
        <v-list-item-avatar>
            <v-icon
                v-text="icon || 'mdi-file'"
                class="mdi-36px"
            ></v-icon>
        </v-list-item-avatar>
        <v-list-item-content>
            <v-list-item-title :class="{'text-decoration-line-through': status == 'canceled'}">
                {{ file.fileName }}
            </v-list-item-title>
            <v-list-item-subtitle>
                <span>{{formatBytes(file.size)}} - {{file.type}}</span>
                <span v-if="isUploading"> - {{uploadedAmount}}%</span>
            </v-list-item-subtitle>
            <span v-if="message" style="color:red;">
                {{ message }}
            </span>
            <span v-if="isUploading">
                <v-progress-linear :value="uploadedAmount"></v-progress-linear>
            </span>
        </v-list-item-content>
        <v-list-item-action>
            <v-btn v-if="status == 'waiting'" icon color="secondary" @click="cancel()">
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <span v-if="isUploading">
                <v-btn small color="primary" class="mr-1"
                    @click="isPaused ? resume() : pause()"
                >
                    {{ trans(isPaused ? "resume" : "pause") }}
                </v-btn>
                <v-btn icon color="secondary" @click="cancel()">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </span>
            <span v-else-if="status == 'success'">{{ trans('Done') }}</span>
            <span v-else-if="status == 'retrying'">{{ trans('Fault, retrying..') }}</span>
            <span v-else-if="status == 'error'">{{ trans('500: Server Error') }}</span>
            <span v-else-if="status == 'canceled'">{{ trans('Cancelled') }}</span>
        </v-list-item-action>
    </v-list-item>
</template>

<script>
import {formatBytes} from "./util";

export default {
    emits: ['cancel'],
    props: [
        'file',
        'status',
        'progress',
        'message',
        'icon'
    ],
    data() {
        return {
            isPaused: false // we upload straight away by default
        }
    },
    computed: {
        isUploading() {
            return this.status == 'uploading'
        },
        uploadedAmount() {
            return (this.progress * 100).toFixed(2)
        },
    },
    methods: {
        formatBytes,
        upload() {
            this.file.resumableObj.upload()
            this.isPaused = false
        },
        pause() {
            this.file.pause()
            this.isPaused = true
        },
        resume() {
            this.pause() // not sure why, but we have to call pause again before upload will resume
            this.upload()
        },
        cancel() {
            this.$emit('cancel', this.file)
        }
    }
}
</script>