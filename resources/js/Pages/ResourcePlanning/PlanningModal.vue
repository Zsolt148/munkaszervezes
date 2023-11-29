<template>
    <v-dialog v-model="isVisible" max-width="500px" persistent @click:outside="close">
        <v-card>
            <v-card-title class="dialog--title">
                {{ form.isCreate ? trans('Create variant') : trans('Edit variant')}}

                <v-btn class="ms-auto" icon light @click="close">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text>
                <v-container>
                    <v-row>
                        <v-col cols="12">
                            <v-text-field
                                v-model="form.name"
                                required
                                label="Előtervezés megnevezése"
                                filled
                                hide-details="auto"
                                persistent-hint
                                :error-messages="form.errors ? form.errors['name'] : null"
                            />
                        </v-col>
                    </v-row>
                </v-container>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        @click="submit"
                        :loading="form.processing"
                        type="submit"
                        class="ms-5"
                        color="primary"
                        
                    >
                    {{ form.isCreate ? trans('Create variant') : trans('Update variant')}}
                </v-btn>
                </v-card-actions>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
import ConfirmsModal from "@/Components/Shared/ConfirmsModal.vue"

export default {
    name: "PlanningModal",
    emits: ['create', 'update'],
    components: {
        ConfirmsModal,
    },
    props: {
        form: Object,
        isVisible: Boolean,
    },
    data() {
        return {
            lang: 'hu',
        };
    },
    methods: {
        close() {
            this.$emit("close");
        },
        submit() {
            if (this.form.isCreate) {
                console.log(this.form.isCreate);
                this.$emit("create", this.form);
            } else {
                console.log(this.form.isCreate);
                this.$emit("update", this.form);
            }
        },
    },
};
</script>