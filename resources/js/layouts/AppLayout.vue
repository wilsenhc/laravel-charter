<script setup lang="ts">
import AppFooter from '@/components/AppFooter.vue';
import AppHeader from '@/components/AppHeader.vue';
</script>

<template>
    <div>
        <div class="animated-gradient" aria-hidden="true"></div>
        <AppHeader />
        <slot />
        <AppFooter />
    </div>
</template>
