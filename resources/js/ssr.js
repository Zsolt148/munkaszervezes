import Vue from 'vue'
import { createRenderer } from 'vue-server-renderer'
import { createInertiaApp } from '@inertiajs/inertia-vue'
import createServer from '@inertiajs/server'

const appName = 'Laravel'

createServer((page) => createInertiaApp({
    page,
    render: createRenderer().renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: name => require(`./Pages/${name}`),
    setup({ app, props, plugin }) {
        Vue.use(plugin)
        return new Vue({
            render: h => h(app, props),
        })
    },
}))
