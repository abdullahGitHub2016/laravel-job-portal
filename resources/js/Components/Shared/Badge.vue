<script setup>
const props = defineProps({ variant: { type: String, default: 'default' } })
const classes = {
  featured: 'bg-amber-100 text-amber-700',
  hot:      'bg-red-100 text-red-600',
  urgent:   'bg-purple-100 text-purple-700',
  success:  'bg-emerald-100 text-emerald-700',
  default:  'bg-slate-100 text-slate-600',
}
</script>

<template>
  <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium"
    :class="classes[variant] ?? classes.default">
    <slot />
  </span>
</template>
