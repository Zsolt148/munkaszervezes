<template>
    <v-form>
        <v-row class="pa-2">
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <v-checkbox
                            v-model="form.email_notifications"
                            :label="'Mindenről szeretnék email értesítést kapni'"
                            :hint="'Bekapcsolt állapotban minden belső értesítésről egy email üzenet is fog menni. Ez akár nagyon sok emailt is jelenthet.'"
                            persistent-hint
                            name="email_notifications"
                        />
                    </v-col>
                </v-row>

                <v-row class="mt-5 d-flex align-items-center justify-content-between">
                    <v-col>
                        <v-btn
                            color="primary"
                            
                            class="me-3"
                            @click="save"
                            :loading="form.processing"
                        >
                            <span>{{ trans('Save') }}</span>
                        </v-btn>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-form>
</template>

<script>
export default {
    name: "NotificationsTab",
    props: ['user'],
    data() {
        return {
            form: this.$inertia.form({
                email_notifications: this.user.email_notifications
            })
        }
    },
    methods: {
        save() {
            this.form.put(this.route('user-notification.update'), {
                preserveScroll: true
            })
        }
    }
}
</script>

<style scoped>

</style>