<template>
    <div class="mt-5 pa-0">
        <v-row class="mt-0">
            <v-col cols="12" class="pt-0">
                <v-card-title class="pt-1 ps-2">
                    <slot name="title"></slot>
                </v-card-title>

                <v-card-subtitle class="ps-2">
                    <slot name="description"></slot>
                </v-card-subtitle>
            </v-col>
        </v-row>

        <v-row class="mt-0">
            <v-col>
                <form @submit.prevent="submitted">
                    <v-col>
                        <div>
                            <slot name="form"></slot>
                        </div>
                    </v-col>

                    <v-col>
                        <slot name="actions"></slot>
                    </v-col>
                </form>
            </v-col>
        </v-row>
    </div>
</template>

<script>
    export default {
        emits: ['submitted'],

        components: {
            //
        },

        methods: {
            submitted(e) {
                if(e.submitter['data-ck-expando'] === undefined) {
                    this.$emit('submitted')
                }
            }
        }
    }
</script>
