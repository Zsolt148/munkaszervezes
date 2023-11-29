<template>
    <v-card flat class="mx-auto" :loading="loading">
        <v-card-text v-if="!uploadDisabled" class="pa-0">
            <div class="dropzone"
                 ref="dropzone"
                 :class="[dragover ? 'dragover-color' : 'default-color']"
                 @drop.prevent="dragover = false"
                 @dragover.prevent="dragover = true"
                 @dragenter.prevent="dragover = true"
                 @dragleave.prevent="dragover = false"
            >
                <div class="dropzone-display">
                    <div class="dropzone--body">
                        <v-icon large>{{ icon }}</v-icon>
                        <h3>{{ trans(label) }}</h3>
                        <span>- {{ trans('or') }} -</span>
                        <v-btn class="dropzone--button" outlined >
                            {{ trans('Add file') }}
                        </v-btn>
                    </div>
                </div>
            </div>
        </v-card-text>
        <v-card-text v-if="files.length" class="pa-0 files-list-wrapper">
            <v-list two-line>
                <file-upload-item
                    v-for="(file, index) in files"
                    v-bind:key="file.file.uniqueIdentifier + index"
                    :file="file.file"
                    :status="file.status"
                    :progress="file.progress"
                    :message="file.message"
                    @cancel="cancelFile"
                />
            </v-list>
        </v-card-text>
    </v-card>
</template>

<script>
import Resumable from "resumablejs";
import FileUploadItem from "./FileUploadItem";
import {formatBytes} from "@/Components/Shared/util";

export default {
    name: "FileUpload",
    components: {
        FileUploadItem
    },
    props: {
        /*
            Use it with model_type (class) and model_id for existing models
            or use it with @uploaded event to get the tmp path
         */
        model_type: { required: false, default: '' },
        model_id: { required: false, default: '' },
        icon: {
            type: String,
            default: 'mdi-file-plus'
        },
        label: {
            type: String,
            default: 'Drag the documents you want to upload here'
        },
        collection: {
            type: String,
            default: 'default'
        },
        url: {
            required: false,
            type: String,
            default() {
                return route('file-upload')
            }
        },
    },
    data() {
        return {
            dragover: false,
            uploadDisabled: false,
            loading: false,
            progress: 0,
            r: false,
            files: [],
            images: [],
        };
    },
    mounted() {
        // init resumablejs on mount
        this.r = new Resumable({
            target: this.url,
            chunkSize: 5 * 1024 * 1024, //5 MB chunks
            query: {
                _token: this.$page.props.csrf_token,
                model_type: this.model_type,
                model_id: this.model_id,
                collection: this.collection
            },
            simultaneousUploads: 1,
            maxChunkRetries: 1,
            testChunks: false,
        });

        // Resumable.js isn't supported, fall back on a different method
        if(!this.r.support) return alert('Your browser doesn\'t support chunked uploads. Get a better browser.');

        this.r.assignBrowse(this.$refs.dropzone);
        this.r.assignDrop(this.$refs.dropzone);

        // set up event listeners to feed into vues reactivity
        this.r.on('fileAdded', async (file, event) => {

            let status = 'uploading'
            let message = null;

            file.hasUploaded = false
            file.type = file.file.type
            file.extension = file.file.type.split("/").pop()

            this.files.push({
                file,
                status: status,
                message: message,
                progress: 0
            })

            this.r.upload()
        })
        this.r.on('fileSuccess', (file, response) => {
            this.findFile(file).status = 'success'

            let resp = JSON.parse(response)
            if (resp.file) {
                this.$emit('uploaded', resp.file)
            }

            this.checkFiles(resp)
        })
        this.r.on('fileError', (file, event) => {
            this.findFile(file).status = 'error'
        })
        this.r.on('fileRetry', (file, event) => {
            this.findFile(file).status = 'retrying'
        })
        this.r.on('fileProgress', (file) => {
            var localFile = this.findFile(file)
            // if we are doing multiple chunks we may get a lower progress number if one chunk response comes back early
            var progress = file.progress()
            if(progress > localFile.progress) {
                localFile.progress = progress
            }
        })
    },
    methods: {
        formatBytes,
        findFile(file) {
            return this.files.find(item => item.file.uniqueIdentifier === file.uniqueIdentifier && item.status !== 'canceled') ?? {}
        },
        checkFiles(response = {}) {
            if (this.uploadingFiles == 0) {
                this.loading = false
                this.flash(response)
            }
        },
        cancelFile(file) {
            this.findFile(file).status = 'canceled'
            file.cancel()
            this.checkFiles()
        },
        close() {
            this.$emit("close");
        },
        flash(response) {
            if (response.success) {
                this.$page.props.flash.success = response.success
            }else if(response.error) {
                this.$page.props.flash.error = response.error
            }
        }
    },
    computed: {
        waitingFiles() {
            return this.files.filter(item => item.status == 'waiting').length
        },
        uploadingFiles() {
            return this.files.filter(item => item.status == 'uploading').length
        }
    }
}
</script>
