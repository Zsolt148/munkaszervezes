<template>
    <div>
        <v-dialog
            v-model="isVisible"
            fullscreen
            hide-overlay
            transition="dialog-bottom-transition"
            @click:outside="close"
        >
            <v-card outlined>
                <v-toolbar dark color="primary">
                    <v-btn icon dark @click="close">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-toolbar-title>{{isCreate ? trans("Create new policy") : trans("Edit policy")}}</v-toolbar-title>
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-card outlined>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <language-select cols="12" :lang.sync="lang" :errors="form.errors" />
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        class="mt-5"
                                        v-model="form.name[lang]"
                                        filled
                                        required
                                        :label="trans('Policy name')"
                                        :error-messages="form.errors ? form.errors['name.' + lang] : null"
                                    >
                                    </v-text-field>

                                    <quill-editor
                                        v-model="form.content[lang]"
                                        :content="form.content[lang]"
                                    />
                                    <small class="error-text" style="color:red" v-if="form.errors.content ? form.errors.content[lang] : false">Fill in the content please</small>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-container class="d-flex justify-end">
                        <confirms-modal @confirmed="destroy" v-if="!isCreate">
                            <v-btn
                                color="error"
                                class="me-4"
                                
                            >
                            <v-icon left>
                                mdi-delete
                            </v-icon>
                                {{ trans('Delete policy') }}
                            </v-btn>
                        </confirms-modal>

                        <v-btn
                            @click="submit"
                            :loading="form.processing"
                            type="submit"
                            color="primary"
                            
                        >
                            <v-icon left>
                                mdi-content-save
                            </v-icon>
                            {{ trans('Save policy') }}
                        </v-btn>
                    </v-container>
                </v-card>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal";

export default {
    name: "PolicyModal",
    props: {
        form: Object,
        isVisible: Boolean,
        isCreate: Boolean
    },
    components:{
        ConfirmsModal
    },
    emits: ['create', 'update', 'destroy'],
    data() {
        return {
            lang: 'hu',
            isDeletePolicy: false,
        };
    },
    methods: {
        close() {
            this.$emit("close");
        },
        submit() {
            if (this.isCreate) {
                this.$emit("create", this.form);
            } else {
                this.$emit("update", this.form);
            }
        },
        destroy() {
            this.$emit('destroy', this.form)
        }
    },
};
</script>
