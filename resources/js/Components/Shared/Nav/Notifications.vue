<template>
    <v-menu
        offset-y left nudge-bottom="14" min-width="230"
        v-model="menu"
        :close-on-content-click="false"
    >
        <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon>
                <v-icon>mdi-bell-outline</v-icon>
                <v-badge top offset-y="1" color="red" overlap :content="unreadNotificationsCount"  v-if="unreadNotificationsCount > 0"></v-badge>
            </v-btn>
        </template>
        <v-card>
            <v-toolbar flat>
                <v-toolbar-title class="ps-1 pb-0">{{trans('Notifications')}}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-tooltip bottom>
                    <template v-slot:activator="{ on }">
                        <v-icon v-if="unreadNotificationsCount > 0" @click="markAllAsRead()" v-on="on">mdi-email-open-outline</v-icon>
                        <v-icon v-else>mdi-email-outline</v-icon>
                    </template>
                    <span>{{ trans('Mark all notifications as read') }}</span>
                </v-tooltip>
            </v-toolbar>
            <v-divider></v-divider>
            <v-card-text class="px-0 py-0">
                <v-list class="py-0" two-line>
                    <v-list-item-group
                        v-if="notifications.length"
                        v-model="selected"
                        multiple
                    >
                        <v-list-item
                            @click="handleNotificationClick(notification)"
                            v-for="notification in notifications.slice(0, 5)"
                            :key="notification.id"
                            active-class="blue--text"
                            class="my-0"
                        >
                            <template v-slot:default="{ active }">
                                <v-layout style="flex-direction:column; max-width:300px">
                                    <v-flex xs12>
                                        <v-list-item-title>{{ notification.data.title }}</v-list-item-title>
                                    </v-flex>
                                    <v-flex xs12>
                                        <v-list-item-subtitle>{{notification.read_at }}{{ notification.data.body }}</v-list-item-subtitle>
                                    </v-flex>
                                </v-layout>
                            </template>
                        </v-list-item>
                    </v-list-item-group>

                    <v-list-item disabled v-else>
                        <v-layout style="flex-direction:column; max-width:300px" class="text-center">
                            <v-flex xs12>
                                <v-list-item-title>{{ trans('All done')}}</v-list-item-title>
                            </v-flex>
                        </v-layout>
                    </v-list-item>

                    <v-divider></v-divider>

                    <v-list-item class="py-0 my-0" @click="$inertia.visit(route('notifications.index'))">
                        <v-layout style="flex-direction:column; max-width:300px" class="text-center">
                            <v-flex xs12>
                                <v-list-item-title>{{ trans('Show more')}}</v-list-item-title>
                            </v-flex>
                            <v-flex xs12>
                                <v-list-item-subtitle class="text-caption">{{ trans('Show all notifications') }}</v-list-item-subtitle>
                            </v-flex>
                        </v-layout>
                    </v-list-item>
                </v-list>
            </v-card-text>
        </v-card>
    </v-menu>
</template>

<script>
import { flash } from "@/Use/helpers"
export default{
    data() {
        return {
            notifications: [],
            selected: [],
            menu: false,
        }
    },
    async mounted() {
        const resp = await axios.get(this.route('notifications.fetch'))
        this.notifications = resp.data.notifications
        this.selected = Object.keys(this.notifications).map(e => parseInt(e))
    },
    methods:{
        handleNotificationClick(notification) {
            this.$inertia.visit(this.route('notifications.show', notification.id))
        },
        async markAllAsRead() {
            const resp = await axios.post(this.route('notifications.read-all'))
            this.notifications = resp.data.notifications
            flash(this, resp.data)
        },
    },
    computed: {
        unreadNotificationsCount() {
            return this.notifications.filter(notification => !notification.read_at).length
        }
    }
}
</script>
