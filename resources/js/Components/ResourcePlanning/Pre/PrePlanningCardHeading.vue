<template>
    <v-row class="worker--card-heading ma-0">
        <v-col class="pa-0 worker--card-heading-name" cols="10">
            <div class="worker--card-names">
                <v-avatar :style="{ 'border': '1.5px solid white' }" size="30">
                    <v-img :src="getProfPicture(user.profile_photo_url, user.name)"></v-img>
                </v-avatar>
                <p class="worker--card-name">{{ user.name }}</p>
            </div>
        </v-col>
    
        <v-col class="worker--card-time" cols="2">
            Munka óra
        </v-col>
    </v-row>
</template>

<script>
    export default{
        name: 'PlanningCardHeading',
        props: {
            user: {
                type: Object,
                required: true
            }
        }
    }
</script>