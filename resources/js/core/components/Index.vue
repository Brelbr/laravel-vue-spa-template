<template>
    <section id="Index">
        <div class="title mb-4">
            {{ title }}
        </div>
        <div class="links">
            <a href="https://github.com/Brelbr/laravel-vue-spa-template">github.com/Brelbr/laravel-vue-spa-template</a>
        </div>
    </section>
</template>

<script>
export default {
    name: 'Index',
    data: () => ({
        title: window.config.appName
    }),
}
</script>

<style lang="scss" scoped>
    .title {
        font-size: 64px;
    }
</style>
