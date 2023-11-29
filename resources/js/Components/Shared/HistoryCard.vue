<template>
    <article>
        <div v-if="!loading" class="mb-5 task--comment-card">
            <div class="task--comment-card-header">
                <span class="task--comment-id">
                    #{{ history.id }}
                </span>
    
                <v-avatar size="30px" class="comment--avatar" v-if="history.causer">
                    <v-img :src="getProfPicture(history.causer.photo, history.causer.name)" />
                </v-avatar>
    
                <span class="task--comment-name" v-if="history.causer">
                    {{ history.causer.name }} {{ history.title }}
                </span>
    
                <small class="ms-auto task--comment-date">{{ dateFormat(history.created_at) }}</small>
            </div>
    
            <div class="task--comment-body">
                <span v-if="extended">{{ history.event_text }}: {{ history.name }}</span>
                <span v-else>{{ history.event_text }}</span>

                <div v-if="history.props.items">
                    <ul>
                        <li v-for="(item, index) in history.props.items" :key="index">
                            {{ item.name }}: {{ item.action }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <v-skeleton-loader v-else boilerplate type="card-avatar, article, actions" />

    </article>
</template>

<script>
export default {
    name: "HistoryCard",
    props: {
        history: Object,
        index: Number,
        extended: Boolean,
        loading: {
            type: Boolean,
            default: false
        }
    }
}
</script>