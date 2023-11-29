<template>
    <v-form>
        <v-row class="mt-5">
            <v-col cols="12" md="6">
                <v-row>
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.name"
                            :label="trans('Full name')"
                            :placehodler="trans('Full name')"
                            :error-messages="form.errors.name"
                            name="name"
                            autocomplete="name"
                            filled
                            type="name"
                            placeholder="Name"
                            hide-details="auto"
                        />
                    </v-col>
        
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.email"
                            :label="trans('Email')"
                            :placehodler="trans('Email')"
                            :error-messages="form.errors.email"
                            name="email"
                            autocomplete="email"
                            filled
                            type="email"
                            placeholder="john@example.com"
                            hide-details="auto"
                        />
                    </v-col>
        
                    <v-col cols="12">
                        <v-text-field
                            v-model="form.phone"
                            :label="trans('Phone')"
                            :placehodler="trans('Phone')"
                            :error-messages="form.errors.phone"
                            name="phone"
                            filled
                            type="tex"
                            hide-details="auto"
                        />
                    </v-col>
                </v-row>
        
                <v-row class="mb-5">
                    <v-col>
                        
                        <v-col>
                            <v-row style="position: relative">
                                <label for="photo" value="Photo" />
                                <input type="file" class="d-none"
                                            ref="photo"
                                            @change="updatePhotoPreview">
                
                                <div class="mt-2" v-show="photoPreview">
                                    <v-avatar size="120" class="me-6">
                                        <v-img
                                            :src="photoPreview"
                                            :alt="user.name"
                                            class="rounded" />
                                    </v-avatar>

                                    <span class="image--uploader" @click="selectNewPhoto" v-show="photoPreview">
                                        <v-icon class="pa-0 m-0" color="white">mdi-replay</v-icon>
                                    </span>
                                </div>
                            </v-row>
                        </v-col>

                        <v-col>
                            <v-row style="position: relative">
                                <v-avatar size="120" class="me-6" v-show="! photoPreview">
                                    <v-img
                                        :src="getProfPicture(user.profile_photo_url, user.name)"
                                        :alt="user.name"
                                        class="rounded" />
                                </v-avatar>
        
                                <span class="image--uploader" @click="selectNewPhoto" v-show="! photoPreview">
                                    <v-icon class="pa-0 m-0" color="white">mdi-replay</v-icon>
                                </span>

                                <v-btn color="primary"  class="mt-4" @click="deletePhoto" v-if="user.profile_photo_path">
                                    {{trans('Remove Photo')}}
                                </v-btn>
                            </v-row>
                        </v-col>
                    </v-col>
        
                    {{ form.errors.photo }}
                </v-row>
        
                <v-row>
                    <v-col cols="12">
                        <v-text-field
                            id="current_password"
                            type="password"
                            ref="current_password"
                            filled
                            :label="trans('Current password')"
                            :placeholder="trans('Current password')"
                            v-model="passwordForm.current_password"
                            :error-messages="passwordForm.errors.current_password"
                            hide-details="auto"
                            autocomplete="current-password" />
                    </v-col>
        
                    <v-col cols="12">
                        <v-text-field
                            id="password"
                            type="password"
                            ref="password"
                            filled
                            :label="trans('New password')"
                            :placeholder="trans('New password')"
                            v-model="passwordForm.password"
                            :error-messages="passwordForm.errors.password"
                            hide-details="auto"
                            autocomplete="password" />
                    </v-col>
        
                    <v-col cols="12">
                        <v-text-field
                            id="password_confirmation"
                            type="password"
                            ref="password_confirmation"
                            filled
                            :label="trans('New password again')"
                            :placeholder="trans('New password again')"
                            v-model="passwordForm.password_confirmation"
                            :error="passwordForm.errors.password_confirmation"
                            hide-details="auto"
                            autocomplete="password_confirmation" />
                    </v-col>

                    <v-col>
                        <v-btn color="primary"  @click="updatePassword" :loading="passwordForm.processing">
                            {{trans("Update password")}}
                        </v-btn>
                    </v-col>
                </v-row>
        
                <v-row class="mt-5 d-flex align-items-center justify-content-between">
                    <v-col>
                        <v-btn
                            color="primary"
                            
                            class="me-3"
                            @click="updateProfileInformation"
                            :loading="form.processing"
                        >
                            <span>{{ trans('Save') }}</span>
                        </v-btn>
                    </v-col>
        
                    <v-col>
                        <confirms-password @confirmed="deleteUser" :must-confirm="true">
                            <v-btn color="error" >
                                {{trans('Delete my profile')}}
                            </v-btn>
                        </confirms-password>
                    </v-col>
                </v-row>
            </v-col>
        </v-row>
    </v-form>
</template>

<script>
import ConfirmsPassword from "@/Components/Shared/ConfirmsPassword.vue";

export default {
    components: {
        ConfirmsPassword,
    },
    props: ['user'],
    data() {
        return {
            form: this.$inertia.form({
                _method: 'PUT',
                name: this.user.name,
                phone: '',
                email: this.user.email,
                photo: null,
            }),

            passwordForm: this.$inertia.form({
                current_password: '',
                password: '',
                password_confirmation: '',
            }),

            deleteUserForm: this.$inertia.form({
                password: '',
            }),

            photoPreview: null,
        }
    },

    methods: {
        updateProfileInformation() {
            if (this.$refs.photo) {
                this.form.photo = this.$refs.photo.files[0]
            }

            this.form.post(this.route('user-profile-information.update'), {
                errorBag: 'updateProfileInformation',
                preserveScroll: true
            });
        },

        selectNewPhoto() {
            this.$refs.photo.click();
        },

        updatePhotoPreview() {
            const reader = new FileReader();

            reader.onload = (e) => {
                this.photoPreview = e.target.result;
            };

            reader.readAsDataURL(this.$refs.photo.files[0]);
        },

        updatePassword() {
            this.passwordForm.put(this.route('user-password.update'), {
                errorBag: 'updatePassword',
                preserveScroll: true,
                onSuccess: () => this.passwordForm.reset(),
                onError: () => {
                    if (this.passwordForm.errors.password) {
                        this.passwordForm.reset('password', 'password_confirmation')
                        this.$refs.password.focus()
                    }

                    if (this.passwordForm.errors.current_password) {
                        this.passwordForm.reset('current_password')
                        this.$refs.current_password.focus()
                    }
                }
            })
        },

        deletePhoto() {
            this.$inertia.delete(this.route('current-user-photo.destroy'), {
                preserveScroll: true,
                onSuccess: () => (this.photoPreview = null),
            });
        },

        deleteUser() {
            this.deleteUserForm.delete(this.route('current-user.destroy'), {
                preserveScroll: true,
                onSuccess: () => {
                    //
                },
                onFinish: () => this.form.reset(),
            })
        },
    },
}
</script>

<style>
    .image--uploader{
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100%;
        cursor: pointer;
        top: 96px;
        left: 37px;
        width: 44px;
        border: 1px solid white;
        height: 44px;
        background-color: #00677d !important;
    }
</style>