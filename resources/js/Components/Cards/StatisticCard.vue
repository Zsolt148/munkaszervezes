<template>
    <v-card outlined>
        <v-row class="ma-0 pa-0">
            <v-col cols="12">
                <v-card-title class="text-no-wrap pt-1 ps-2"> {{ title }} </v-card-title>
                <v-card-subtitle class="text-no-wrap ps-2"> {{ subtitle }} </v-card-subtitle>
                <v-card-text v-if="cardValue && linkText" class="d-flex align-center mt-2 pb-2 ps-2">
                    <div>
                        <p class="text-xl font-weight-semibold primary--text mb-2">{{ cardValue }}</p>
                        <v-btn v-if="link" small color="primary"> {{ linkText }} </v-btn>
                    </div>
                </v-card-text>

                <v-card-text>
                    <v-row>
                        <v-col
                            v-for="stat in statistics"
                            cols="12"
                            md="4"
                            class="d-flex align-center"
                        >
                        <v-avatar
                            size="44"
                            color="primary"
                            rounded
                        >
                            <v-icon
                                dark
                                color="white"
                                size="30"
                            >
                            {{ stat.icon }}
                            </v-icon>
                        </v-avatar>
                        <div class="ms-3">
                            <p class="text-xs mb-0">
                            {{ stat.title }}
                            </p>
                            <h3 class="text-xl font-weight-semibold">
                            {{ stat.value }}
                            </h3>
                        </div>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-col>
        </v-row>
    </v-card>
</template>

<script>
export default {
    name: "StatisticCard",
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
        },
        statistics:{
            type: Array,
            required: true
        }
    },
}
</script>