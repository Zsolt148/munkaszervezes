<template>
    <v-dialog v-model="isVisible" max-width="800px" persistent @click:outside="close">
        <v-card v-if="log">
            <v-card-title>
                <span>
                    {{ log.name }} - {{ log.event }}
                    <span v-if="log.causer && log.event != 'login'">({{ log.causer.name }})</span>
                </span>
                <v-spacer></v-spacer>
                <div>
                    <v-btn icon light @click="close">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </div>
            </v-card-title>
            <v-card-subtitle>
                {{ dateFormat(log.created_at) }}
            </v-card-subtitle>
            <v-card-text class="p-4">
                <v-row class="d-flex flex-column">
                    <div v-if="log.old_values">
                        <table class="changes__table">
                            <thead>
                                <tr>
                                    <th>{{ trans('Field') }}</th>
                                    <th>{{ trans('Old data') }}</th>
                                    <th>{{ trans('New data') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, name) in log.old_values" v-if="name != 'data'" :key="name">
                                    <td>{{ trans(name) ? trans(name) : '-' }}</td>
                                    <td>{{ getValue(item, name) }}</td>
                                    <td>{{ getValue(log.new_values[name], name) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="log.properties.attributes">
                        <table class="changes__table">
                            <thead>
                                <tr>
                                    <th>{{ trans('Field') }}</th>
                                    <th>{{ trans('Value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, name) in log.properties.attributes" v-if="name != 'data'" :key="name">
                                    <td>{{ trans(name) ? trans(name) : '-' }}</td>
                                    <td>{{ getValue(item, name) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="log.properties.length">
                        <table class="changes__table">
                            <thead>
                                <tr>
                                    <th>{{ trans('Field') }}</th>
                                    <th>{{ trans('Value') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, name) in log.properties" :key="name">
                                    <td>{{ trans(name) ? trans(name) : '-' }}</td>
                                    <td>{{ getValue(item, name) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-else-if="log.properties.items">
                        <div class="d-flex flex-column">
                            <span>{{ log.event_text }}: {{ log.name }}</span>
                            <ul>
                                <li v-for="(item, index) in log.properties.items" :key="index">
                                    {{ item.name }}: {{ item.action }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </v-row>
            </v-card-text>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "LogsModal",
    props: {
        log: Object,
        isVisible: Boolean,
    },
    methods: {
        close() {
            this.$emit("close");
        },
        getValue(value, name) {
            if (value) {
                if (['created_at', 'updated_at', 'deleted_at', 'blocked_at', 'last_login_at'].includes(name)) {
                    var timestamp = Date.parse(value)
                    if (!isNaN(timestamp)) {
                        return this.dateFormat(value)
                    }
                }
                return value
            }
            return '-';
        }
    },
};
</script>

<style scoped>
.changes__table {
    width: 100%;
    justify-content: space-between;
    margin-top: 30px;
}

.changes__table table {
    width: 100%;
    border-collapse: collapse;
}

.changes__table th,
.changes__table td {
    padding: 10px;
    width: 33.33%;
    border-bottom: thin solid rgba(0, 0, 0, 0.12);
}
</style>
