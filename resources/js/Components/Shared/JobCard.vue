<script setup>
import { Link } from '@inertiajs/vue3'
defineProps({ job: Object })
</script>

<template>
  <Link :href="route('jobs.show', job.slug)"
    class="group block bg-white rounded-xl border border-slate-100 shadow-sm hover:shadow-md hover:border-emerald-200 transition-all duration-200 p-5">
    <div class="flex items-start gap-4">
      <div class="w-12 h-12 rounded-lg bg-slate-100 flex-shrink-0 overflow-hidden flex items-center justify-center">
        <img v-if="job.employer?.logo" :src="job.employer.logo" :alt="job.employer.name" class="w-full h-full object-cover"/>
        <span v-else class="text-lg font-bold text-slate-400">{{ job.employer?.name?.charAt(0) ?? '?' }}</span>
      </div>
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap gap-1.5 mb-1.5">
          <span v-if="job.is_featured" class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-medium rounded-full">⭐ Featured</span>
          <span v-if="job.is_hot"      class="px-2 py-0.5 bg-red-100 text-red-600 text-xs font-medium rounded-full">🔥 Hot</span>
          <span v-if="job.is_urgent"   class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">⚡ Urgent</span>
        </div>
        <h3 class="font-semibold text-slate-900 group-hover:text-emerald-700 transition-colors truncate">{{ job.title }}</h3>
        <div class="flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1 text-sm text-slate-500">
          <span class="font-medium text-slate-700">{{ job.employer?.name }}</span>
          <span>{{ job.district }}</span>
        </div>
        <div class="flex flex-wrap gap-2 mt-3">
          <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">{{ job.job_type_label }}</span>
          <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-xs font-medium">{{ job.salary_display }}</span>
          <span v-if="job.category" class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-xs">{{ job.category?.name }}</span>
        </div>
      </div>
      <div class="flex-shrink-0 text-right text-xs text-slate-400 space-y-1">
        <div>{{ job.published_at }}</div>
        <div class="font-medium" :class="(job.days_remaining ?? 99) <= 3 ? 'text-red-500' : 'text-slate-500'">
          {{ job.days_remaining }}d left
        </div>
      </div>
    </div>
  </Link>
</template>
