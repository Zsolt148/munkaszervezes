<template>
    <div class="comment--container" outlined>

        <ck-editor v-if="showNewCommentWriter" :content.sync="body" :loading="loading" inline @save="saveComment" />

        <v-card-actions v-else>
            <v-spacer></v-spacer>
            <v-btn type="submit" color="primary" normal  @click.prevent="toggleNewCommentWriter">
                {{ trans("Write new comment") }}
            </v-btn>
        </v-card-actions>

        <template>
            <v-card v-for="(comment, index) in comments" :key="comment.id" class="comment--card" outlined>
                <v-list-item-content class="py-0">
                    <div class="comment--header">
                        <p class="comment--id">
                            # {{ comment.id }}
                        </p>

                        <v-avatar size="30" class="comment--avatar">
                            <v-img :src="getProfPicture(comment.admin.photo, comment.admin.name)"/>
                        </v-avatar>

                        <p class="comment--name">
                            {{ comment.admin.name }} - {{ trans('commented on task') }}
                        </p>

                        <small class="comment--time">{{ comment.created_at }}</small>
                    </div>

                    <v-row style="overflow-x: auto">
                        <v-col cols="12">
                            <v-list-item-content class="comment--body pb-0" v-html="comment.body"
                                                 v-if="!comment.isEditing"></v-list-item-content>
                            <ck-editor class="pa-3" v-else :content.sync="updatedBody"/>
                        </v-col>
                        <v-col cols="12" class="pt-0">
                            <div v-if="isOwnComment(comment.admin.id)" class="d-flex">
                                <v-spacer></v-spacer>
                                <v-btn
                                    v-if="isEditingComment(comment)"
                                    @click="updateComment(comment)"
                                    
                                    color="primary"
                                    text
                                    small
                                    :disabled="!updatedBody"
                                    :loading="updateLoading">
                                    <v-icon color="primary" small>
                                        mdi-check
                                    </v-icon>
                                </v-btn>
                                <v-btn  text small @click="cancelEditing(comment)"
                                       v-if="isEditingComment(comment)">
                                    <v-icon color="primary" small>
                                        mdi-close
                                    </v-icon>
                                </v-btn>
                                <v-btn
                                    v-if="!isEditingComment(comment)"
                                    @click.prevent="editComment(comment)"
                                    
                                    small
                                    text>
                                    <v-icon color="primary" small>
                                        mdi-pencil
                                    </v-icon>
                                </v-btn>
                                <confirms-modal @confirmed="deleteComment(comment)">
                                    <v-btn text  small>
                                        <v-icon color="red" small>
                                            mdi-trash-can
                                        </v-icon>
                                    </v-btn>
                                </confirms-modal>
                            </div>

                        </v-col>
                    </v-row>
                </v-list-item-content>
            </v-card>
        </template>

        <v-pagination
            v-model="options.page"
            @input="fetchComments"
            :total-visible="options.commentsPerPage"
            :length="totalCommens"
        />

        <div v-if="comments.length === 0">
            <p class="mt-5 text-center">{{ trans('There are no comments.') }}</p>
        </div>
    </div>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal.vue";
import axios from "axios";
import CkEditor from "@/Components/Ckeditor/CkEditor";

export default {
    name: "TaskComments",
    props: {
        task: {
            type: Object,
            required: true
        }
    },
    components: {
        ConfirmsModal,
        CkEditor
    },
    data() {
        return {
            updateLoading: false,
            comments: [],
            options: {
                page: 1,
                commentsPerPage: 5,
            },
            showNewCommentWriter: true,
            totalCommens: null,
            editorType: 'comment',
            loading: true,
            body: '',
            updatedBody: '',
            currentlyEditing: null,
        };
    },
    methods: {
        async fetchComments() {
            await axios.get(this.route('tasks.get-comments', {
                id: this.task.id,
                page: this.options.page,
                per_page: this.options.commentsPerPage,
            }))
                .then((response) => {
                    this.comments = response.data.data
                    this.totalCommens = response.data.total
                })
                .catch((error) => {
                    console.log(error)
                })
                .finally(() => {
                    this.loading = false
                });
        },

        isOwnComment(id) {
            return id === this.$page.props.auth.user.id;
        },

        toggleNewCommentWriter() {
            this.currentlyEditing = null;

            this.comments.forEach(element => {
                element.isEditing = false;
            });

            this.showNewCommentWriter = !this.showNewCommentWriter;
        },

        isEditingComment(comment) {
            return this.currentlyEditing === comment.id;
        },

        cancelEditing(comment) {
            this.currentlyEditing = null;
            comment.isEditing = false;
            this.showNewCommentWriter = true
        },

        editComment(comment) {
            this.showNewCommentWriter = false;
            if (this.currentlyEditing !== comment.id) {
                if (this.currentlyEditing !== null) {
                    const previousComment = this.comments.find(
                        (c) => c.id === this.currentlyEditing
                    );
                    previousComment.isEditing = false;
                }
                this.currentlyEditing = comment.id;
                comment.isEditing = true;
                this.updatedBody = comment.body;
            }
        },

        async updateComment(comment) {
            this.updateLoading = true;
            const resp = await axios.put(this.route('tasks.update-comment', comment), {
                updatedBody: this.updatedBody
            });
            this.updatedBody = '';
            comment.isEditing = false;
            this.currentlyEditing = null;
            this.updateLoading = false;
            this.fetchComments();
        },

        async deleteComment(comment) {
            const resp = await axios.delete(this.route("tasks.delete-comment", comment));
            this.fetchComments();
        },

        async saveComment() {
            this.loading = true
            const resp = await axios.post(this.route("tasks.store-comment", this.task.id), {
                body: this.body
            })
            this.body = "";
            this.loading = false;
            this.fetchComments();
        },

        shouldShowDivider(index) {
            return index !== 0;
        },

        showUser(user) {
            this.$inertia.visit(this.route("admins.show", user));
        },
    },

    mounted() {
        this.fetchComments();
    },

    watch: {
        task() {
            this.fetchComments();
        },
    },
};
</script>
