<template>
    <div>
    <span @click="startConfirmingPassword">
        <slot />
    </span>

        <v-dialog
            v-model="confirmingPassword"
            max-width="577px"
        >
            <v-card>
                <v-card-title class="dialog--title">
                    {{ trans(title) }}

                    <v-btn class="ms-auto" icon light @click="closeModal()">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-card-title>

                <v-card-text>
                    <p class="dialog--content">{{ trans(content) }}</p>

                    <v-row class="mt-4 mb-2">
                        <v-col class="py-0">
                            <v-text-field
                                ref="password"
                                name="password"
                                type="password"
                                filled
                                hide-details="auto"
                                v-model="form.password"
                                :type="isPasswordVisible ? 'text' : 'password'"
                                :error-messages="form.error"
                                :label="trans('Enter my password')"
                                :append-icon=" isPasswordVisible? 'mdi-eye-off-outline': 'mdi-eye-outline'"
                                @click:append="isPasswordVisible = !isPasswordVisible"
                                @keyup.enter="confirmPassword"
                            />
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-actions class="px-5 pb-5 d-flex justify-space-between">
                    <v-btn
                        class="text-none button"
                        
                        type="button"
                        :color="color"
                        @click="confirmPassword"
                        :loading="form.processing">
                        {{trans(button)}}
                    </v-btn>

                    <v-btn
                        class="text-none button"
                        
                        color="primary"
                        filled
                        type="button"
                        @click="confirmingPassword = false">
                        {{trans('Cancel')}}
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
export default {
    name: "ConfirmsPassword",
    emits: ['confirmed'],
    props: {
        mustConfirm: false,
        title: {
            default: 'Confirm Password',
        },
        content: {
            default: 'For your security, please confirm your password to continue.',
        },
        button: {
            default: 'Confirm',
        },
        color: {
            default: 'primary'
        },
    },
    data() {
        return {
            confirmingPassword: false,
            isPasswordVisible: false,
            form: {
                processing: false,
                password: '',
                error: null,
            }
        }
    },
    methods: {
        startConfirmingPassword() {
            if (this.mustConfirm) {
                this.openModal()
            }else {
                axios.get(this.route('password.confi')).then(response => {
                    if (response.data.confirmed) {
                        this.$emit('confirmed');
                    } else {
                        this.openModal()
                    }
                })
            }
        },

        confirmPassword() {
            this.form.processing = true
            axios.post(this.route('confirm.password'), {
                password: this.form.password,
            }).then((resp) => {
                this.form.processing = false;
                this.closeModal();
                this.$nextTick(() => this.$emit('confirmed', resp.data.password));
            }).catch(error => {
                this.form.processing = false;
                this.form.error = error.response.data.errors.password[0];
                this.$refs.password.focus();
            });
        },

        openModal() {
            this.confirmingPassword = true;
            setTimeout(() => this.$refs.password.focus(), 250)
        },

        closeModal() {
            this.confirmingPassword = false
            this.form.password = '';
        },
    }
}
</script>
