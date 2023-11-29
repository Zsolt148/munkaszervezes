<template>
    <app-layout>
        <app-head title="Notifications" />

        <v-card outlined class="mt-4 pa-4">

            <v-card-title>
                {{ notification.data.title }}
            </v-card-title>

            <v-card-subtitle>
                <v-icon>mdi-email-outline</v-icon>
                {{ dateFormat(notification.created_at) }}
            </v-card-subtitle>

            <v-card-text>
                <v-row>
                    <v-col cols="12">
                        <div v-html="notification.data.body" />
                    </v-col>

                    <v-col cols="12">
                        <v-btn
                            color="secondary"
                            class="me-2"
                            filled
                            outlined
                            @click="$inertia.visit(route('notifications.index'))"
                        >
                            {{ trans('Back') }}
                        </v-btn>
                        <v-btn
                            v-if="!notification.read_at"
                            color="primary"
                            outlined
                            type="submit"
                            @click="read"
                            :loading="form.processing"
                            filled
                        >
                            {{ trans('Mark as read') }}
                        </v-btn>
                        <v-btn
                            v-else
                            color="primary"
                            outlined
                            type="submit"
                            @click="unread"
                            :loading="form.processing"
                            filled
                        >
                            {{ trans('Mark as unread') }}
                        </v-btn>
                        <v-btn
                            v-show="notification.data.route"
                            class="ml-2"
                            color="primary"
                            
                            @click="$inertia.visit(notification.data.route)"
                        >
                            {{ trans('View') }}
                        </v-btn>
                    </v-col>
                </v-row>
            </v-card-text>
        </v-card>
    </app-layout>
</template>
<script>

import AppLayout from "@/Layouts/AppLayout";

export default {
    name: "Show",
    props: ['notification'],
    components: {
        AppLayout,
    },
    data(){
        return{
            form: this.$inertia.form({
                redirect: true
            }),
        }
    },
    methods: {
        read() {
            this.form.post(this.route('notifications.read', this.notification.id))
        },
        unread() {
            this.form.post(this.route('notifications.unread', this.notification.id))
        },
    }
}
</script>
