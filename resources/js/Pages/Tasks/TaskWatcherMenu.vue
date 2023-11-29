<template>
    <v-menu min-width="300px" bottom left class="ms-auto" offset-y open-on-click>
        <template v-slot:activator="{ on, attrs }">
            <v-btn
                class="d-inline ms-auto"
                icon
                color="primary"
                @click="getWatchers()"
                v-bind="attrs"
                v-on="on"
            >
                <v-icon>mdi-eye</v-icon>
            </v-btn>
        </template>
        <v-list>
            <v-list-item @click="watchTask" v-if="!isWatching">
                <small>
                    <v-icon class="me-2">mdi-eye</v-icon>
                    {{ trans('Watch issue') }}
                </small>
            </v-list-item>

            <v-list-item @click="unWatchTask" v-if="isWatching">
                <small> 
                    <v-icon class="me-2">mdi-eye-off</v-icon>
                    {{ trans('Unwatch issue') }}
                </small>
            </v-list-item>
        
            <v-divider v-if="watchers.length"/>
            <v-subheader v-if="watchers.length">{{ trans('Watching this issue') }}</v-subheader>

            <v-list-item v-for="watcher in watchers" :key="watcher.id">
                <v-list-item-avatar  size="25px">
                    <v-img :src="getProfPicture(watcher.profile_photo_url, watcher.name)"></v-img>
                </v-list-item-avatar>

                <v-list-item-content>
                    <v-list-item-subtitle>{{watcher.name}}</v-list-item-subtitle>
                </v-list-item-content>
            </v-list-item>  
        </v-list>
    </v-menu>
</template>

<script>
import { EventBus } from '@/Components/Shared/eventbus'

export default{
    name: 'TaskWatcherMenu',
    props: ['taskId', 'isWatching'],
    data(){
        return{
            form: this.$inertia.form({
                id: this.taskId
            }),
            watchers: []
        }
    },
    methods:{
        watchTask() {
            this.form.post(this.route('tasks.watch-task', this.taskId), {
                onSuccess: () => {
                    EventBus.$emit('isWatching', true);
                }
            })
        },
        
        unWatchTask() {
            this.form.post(this.route('tasks.unwatch-task', this.taskId), {
                onSuccess: () => {
                    EventBus.$emit('isWatching', false);
                }
            })
        },

        getWatchers() {
            this.fetchWatchers();
        },

        async fetchWatchers() {
            await axios.get(this.route('tasks.get-watchers', { id: this.taskId }))
                .then((response) => {
                    this.watchers = response.data;
                })
                .catch((error) => {
                    console.log(error);
                })
        },
    }
}
</script>