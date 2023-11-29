<template>
    <form-section @submitted="saveSecurity">
        <template #title>
            {{trans("Toggle Htaccess securiry")}}
        </template>

        <template #description>
            {{ trans("If htaccess security is turned on, then visitors will have fill given username and password to reach the website") }}
        </template>

        <template #form>
            <v-row>
                <v-col cols="12">
                    <v-text-field
                        type="text"
                        filled
                        v-model="form.username"
                        :error-messages="form.errors.username"
                        :label="trans('Username')"
                        :placeholder="trans('Username to access page while htaccess security mode is active')"
                        hide-details="auto" />
                </v-col>

                <v-col cols="12">
                    <v-text-field
                        :type="isPasswordVisible ? 'text' : 'password'"
                        filled
                        v-model="form.password"
                        :error-messages="form.errors.password"
                        :label="trans('Password')"
                        :placeholder="trans('Password')"
                        :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                        hide-details="auto"
                        @click:append="isPasswordVisible = !isPasswordVisible"
                    />
                </v-col>
            </v-row>
        </template>

        <template #actions>
            <v-btn type="submit" class="mx-2 mt-0 float-right" right color="primary" >
                <v-icon left>
                    mdi-content-save
                </v-icon>
                {{trans('Save changes')}}
            </v-btn>
        </template>
    </form-section>
</template>

<script>
import FormSection from '@/Components/Shared/FormSection.vue'
import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";

export default {
    components: {
        FormSection,
        ConfirmsPassword
    },
    inject: ['settings'],
    data() {
        return {
            isPasswordVisible: false,
            form: this.$inertia.form({
                username: this.settings.htaccess_username,
                password: this.settings.htaccess_password,
            }),
        }
    },
    methods: {
        saveSecurity() {
            this.form.post(this.route('settings.saveSecurity'),{
                preserveScroll: true,
            })
        }
    },
}
</script>
