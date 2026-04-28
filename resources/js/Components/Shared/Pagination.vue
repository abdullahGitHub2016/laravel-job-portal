<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ links: Array })
</script>

<template>
  <nav v-if="links?.length > 3" class="flex flex-wrap gap-1 justify-center">
    <template v-for="link in links" :key="link.label">
      <Link v-if="link.url" :href="link.url" preserve-scroll
        class="px-3 py-1.5 rounded-lg text-sm transition-colors"
        :class="link.active ? 'bg-emerald-500 text-white font-semibold' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'"
        v-html="link.label" />
      <span v-else class="px-3 py-1.5 rounded-lg text-sm text-slate-300 bg-white border border-slate-100" v-html="link.label" />
    </template>
  </nav>
</template>
