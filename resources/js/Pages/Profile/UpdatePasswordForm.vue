<template>
    <form-section @submitted="updatePassword">
        <template #title>
            {{trans('Update Password')}}
        </template>

        <template #description>
            {{trans('Ensure your account is using a long, random password to stay secure.')}}
        </template>

        <template #form>
            <v-row>
            <v-col>
                <v-text-field
                    id="current_password"
                    type="password"
                    ref="current_password"
                    filled
                    :label="trans('Current password')"
                    :placeholder="trans('Current password')"
                    v-model="form.current_password"
                    :error-messages="form.errors.current_password"
                    hide-details="auto"
                    autocomplete="current-password" />
            </v-col>

            <v-col>
                <v-text-field
                    id="password"
                    type="password"
                    ref="password"
                    filled
                    :label="trans('New password')"
                    :placeholder="trans('New password')"
                    v-model="form.password"
                    :error-messages="form.errors.password"
                    hide-details="auto"
                    autocomplete="password" />
            </v-col>

            <v-col>
                <v-text-field
                    id="password_confirmation"
                    type="password"
                    ref="password_confirmation"
                    filled
                    :label="trans('New password again')"
                    :placeholder="trans('New password again')"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    hide-details="auto"
                    autocomplete="password_confirmation" />
            </v-col>
            </v-row>
        </template>

        <template #actions>
            <v-btn type="submit" color="primary"  :loading="form.processing">
                <v-icon left>
                    mdi-content-save
                </v-icon>
                {{trans("Save")}}
            </v-btn>
        </template>
    </form-section>
</template>

<script>
import FormSection from "@/Components/Shared/FormSection.vue";

export default {
    components: {
        FormSection
    },

    data() {
        return {
            form: this.$inertia.form({
                current_password: '',
                password: '',
                password_confirmation: '',
            }),
        }
    },

    methods: {
        updatePassword() {
            this.form.put(this.route('user-password.update'), {
                errorBag: 'updatePassword',
                preserveScroll: true,
                onSuccess: () => this.form.reset(),
                onError: () => {
                    if (this.form.errors.password) {
                        this.form.reset('password', 'password_confirmation')
                        this.$refs.password.focus()
                    }

                    if (this.form.errors.current_password) {
                        this.form.reset('current_password')
                        this.$refs.current_password.focus()
                    }
                }
            })
        },
    },
}
</script>
