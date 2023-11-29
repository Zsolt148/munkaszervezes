<template>
    <v-card outlined>
        <v-row class="ma-0 pa-0">
            <v-col cols="12">
                <v-card-title class="text-no-wrap pt-1 ps-2"> {{ title }} </v-card-title>
                <v-card-subtitle class="text-no-wrap ps-2"> {{ subtitle }} </v-card-subtitle>
                <v-card-text class="d-flex align-center mt-2 pb-2 ps-2">
                    <div>
                        <p class="text-xl font-weight-semibold primary--text mb-2">{{ cardValue }}</p>
                        <v-btn v-if="link" small color="primary"> {{ linkText }} </v-btn>
                    </div>
                </v-card-text>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
    export default {
        props: {
            title: {
                type: String,
                required: true,
            },
            subtitle: {
                type: String,
                required: true,
            },
            link: {
                type: String,
                required: false,
            },
            linkText: {
                type: String,
                required: false,
            },
            cardValue: {
                type: String,
                required: false,
            }
        },
    }
</script>