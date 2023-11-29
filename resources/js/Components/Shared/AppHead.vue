<template>
    <Head :title="title ? `${trans(title)} - ERP` : 'ERP'">
        <slot />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <link rel="icon" type="image/png" :href="$page.props.page.favicon"/>
        <meta name="description" :content="$page.props.page.description">
        <meta name="revisit-after" content="18 days">
        <meta name="author" :content="$page.props.page.author">
    </Head>
</template>

<script>
import { Head } from '@inertiajs/inertia-vue'

export default {
    name: "AppHead",
    components: {
        Head,
    },
    props: {
        title: String,
    },
}
</script>
