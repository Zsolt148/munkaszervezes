<template>
    <div>
        <div v-if="inline">
            <v-text-field
                v-if="!inFocus"
                :label="label ? label : 'Kattints ide a gépeléshez..'"
                filled
                dense
                hide-details
                @focus="inFocus = true"
            />

            <div v-if="inFocus">
                <ckeditor id="ckeditor" :editor="ckeditor" v-model="vmodel" :disabled="disabled" @ready="onEditorReady" />

                <small v-if="error" class="error--text">
                    {{ error }}
                </small>

                <div class="d-flex mt-4">
                    <v-spacer></v-spacer>
                    <v-btn color="secondary"
                           outlined
                           normal
                           class="me-2"
                           @click.prevent="inFocus = false">
                        {{ trans("Cancel") }}
                    </v-btn>
                    <v-btn color="primary" normal 
                           @click.prevent="$emit('save')" :loading="loading" :disabled="!vmodel">
                        {{ trans("Save") }}
                    </v-btn>
                </div>
            </div>
        </div>
        <div v-else>

            <label for="ckeditor" v-if="label">
                {{ label }}
            </label>

            <ckeditor id="ckeditor" :editor="ckeditor" v-model="vmodel" :disabled="disabled" @ready="onEditorReady" />

            <small v-if="error" className="error-text" style="color:red">
                {{ error }}
            </small>
        </div>
    </div>
</template>

<script>
/*
    Usage:
    import CkEditor from "@/Components/Ckeditor/CkEditor";
    <ck-editor :label="trans('Content')" :content.sync="form.content" :error="form.errors.content" />
 */

import ClassicEditor from "./config";
import CKEditor from '@ckeditor/ckeditor5-vue2';
import {EventBus} from '@/Components/Shared/eventbus'

export default {
    name: "CkEditor",
    components: {
        ckeditor: CKEditor.component,
    },
    props: {
        // label
        label: {type: String, default: null, required: false},
        // content.sync (v-model)
        content: {type: String|null, required: true, default: ''},
        //disabled
        disabled: {type: Boolean, required: false, default: false},
        // error messages
        error: {type: String, default: null, required: false},
        // custom editor config
        editor: {type: Function, default: null, required: false},
        // loading
        loading: Boolean,
        // shoud be inline - clickable v-text-field
        inline: Boolean,
    },
    data() {
        return {
            ckeditor: this.editor ? this.editor : ClassicEditor,
            fileManagerIsOpen: false,
            vmodel: this.content,
            editorId: null,
            inFocus: false,
        }
    },
    watch: {
        content(val) {
            this.vmodel = val ? val : ''
        },
        vmodel(value) {
            this.$emit('update:content', value)
        },
    },
    methods: {
        toggleFileManager() {
            this.fileManagerIsOpen = !this.fileManagerIsOpen
        },
        onEditorReady(editor) {
            if (this.inline) {
                editor.focus();
            }
            this.editorId = editor.id
            EventBus.$on('toggle-filemanager-' + this.editorId, this.toggleFileManager.bind(this))
        }
    },
}
</script>
