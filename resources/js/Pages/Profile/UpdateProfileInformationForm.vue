<template>
    <form-section @submitted="updateProfileInformation">
        <template #title>
            {{trans('Personal information')}}
        </template>

        <template #description>
            {{trans("Update your account's profile information and email address.")}}
        </template>

        <template #form>
            <v-row class="mt-0">
                <v-col class="pt-0">
                    <label for="photo" value="Photo" />
                    <input type="file" class="d-none"
                                ref="photo"
                                @change="updatePhotoPreview">

                    <div class="mt-2" v-show="photoPreview">
                        <span
                            class="block rounded"
                            :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
                        </span>
                    </div>

                    <v-col>
                        <v-row>
                            <v-avatar size="120" class="me-6" v-show="! photoPreview">
                                <v-img
                                    :src="getProfPicture(user.profile_photo_url, user.name)"
                                    :alt="user.name"
                                    class="rounded" />
                            </v-avatar >

                            <v-btn color="primary"  class="mt-4 ms-2 mr-2" @click="selectNewPhoto">
                                {{trans('New Photo')}}
                            </v-btn>

                            <v-btn color="primary"  class="mt-4" @click="deletePhoto" v-if="user.profile_photo_path">
                                {{trans('Remove Photo')}}
                            </v-btn>
                        </v-row>

                        <p class="text-sm mt-5">
                            {{ trans("Allowed JPG, GIF or PNG. Max size of 800K") }}
                        </p>
                    </v-col>

                </v-col>

                {{ form.errors.photo }}
            </v-row>

            <v-row class="mt-2">
                <v-col cols="4">
                    <v-text-field
                        v-model="form.name"
                        name="name"
                        autocomplete="name"
                        filled
                        :label="trans('Name')"
                        type="name"
                        placeholder="Name"
                        :error-messages="form.errors.name"
                        hide-details="auto"
                        class="mb-3"
                    />
                </v-col>

                <v-col cols="4">
                    <v-text-field
                        v-model="form.email"
                        name="email"
                        autocomplete="email"
                        filled
                        label="Email"
                        type="email"
                        placeholder="john@example.com"
                        :error-messages="form.errors.email"
                        hide-details="auto"
                        class="mb-3"
                    />
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
            FormSection,
        },
        props: ['user'],
        data() {
            return {
                form: this.$inertia.form({
                    _method: 'PUT',
                    name: this.user.name,
                    email: this.user.email,
                    photo: null,
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

                console.log(this.$refs.photo.files[0])
                reader.readAsDataURL(this.$refs.photo.files[0]);
            },

            deletePhoto() {
                this.$inertia.delete(this.route('current-user-photo.destroy'), {
                    preserveScroll: true,
                    onSuccess: () => (this.photoPreview = null),
                });
            },
        },
    }
</script>
