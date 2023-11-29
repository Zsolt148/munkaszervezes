<template>
    <v-row>
        <v-col>
            <v-select
                :items="roles"
                v-model="selectedRoles"
                :placeholder="trans('Role')"
                clearable
                multiple
                deletable-chips
                small-chips
                hide-details="auto"
                dense
                filled
                @change="getResponsiblesByRoles"
            />
        </v-col>

        <v-col>
            <v-select
                :items="responsibles"
                v-model="selectedResponsibles"
                :disabled="!selectedRoles.length"
                :placeholder="trans('Responsible')"
                :loading="loading"
                name="responsibles"
                item-text="name"
                item-value="id"
                clearable
                multiple
                deletable-chips
                small-chips
                hide-details="auto"
                dense
                filled
            />
        </v-col>
    </v-row>
</template>

<script>
export default {
    name: "ResponsiblePicker",
    props: ['roles'],
    data() {
        return {
            responsibles: [],
            selectedRoles: [],
            selectedResponsibles: [],
            loading: false,
        }
    },
    methods: {
        async getResponsiblesByRoles() {
            this.loading = true
            this.selectedResponsibles = [];
            const response = await axios.get(this.route('tasks.get-responsibles'), {
                params: {
                    roles: this.selectedRoles
                }
            });
            this.responsibles = response.data;
            this.loading = false
        },

        change() {
            this.$emit('change', {
                roles: this.selectedRoles,
                responsibles: this.selectedResponsibles
            })
        }
    },
    watch: {
        selectedResponsibles() {
            this.change();
        }
    },
}
</script>

<style scoped>

</style>