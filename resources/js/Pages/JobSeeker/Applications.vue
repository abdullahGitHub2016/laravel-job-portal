<script setup>
import { Link } from '@inertiajs/vue3'
import SeekerLayout from '@/Layouts/SeekerLayout.vue'

defineProps({ applications: Object })

const STATUS_COLORS = {
  pending:     'bg-yellow-100 text-yellow-700',
  reviewed:    'bg-blue-100 text-blue-700',
  shortlisted: 'bg-indigo-100 text-indigo-700',
  interview:   'bg-purple-100 text-purple-700',
  offered:     'bg-amber-100 text-amber-700',
  hired:       'bg-bd-pink-100 text-bd-pink-700',
  rejected:    'bg-red-100 text-red-500',
  withdrawn:   'bg-slate-100 text-slate-400',
}
</script>

<template>
  <SeekerLayout title="My Applications">
    <div class="space-y-4">
      <div v-for="app in applications.data" :key="app.id"
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center font-bold text-slate-400 flex-shrink-0">
          {{ app.job_post?.employer_profile?.company_name?.charAt(0) ?? '?' }}
        </div>
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-slate-800 truncate">{{ app.job_post?.title }}</p>
          <p class="text-sm text-slate-500">{{ app.job_post?.employer_profile?.company_name }}</p>
          <p class="text-xs text-slate-400 mt-0.5">Applied {{ new Date(app.created_at).toLocaleDateString() }}</p>
        </div>
        <span class="px-3 py-1 text-xs font-medium rounded-full self-start sm:flex-shrink-0"
          :class="STATUS_COLORS[app.status] ?? 'bg-slate-100 text-slate-500'">
          {{ app.status?.replace('_', ' ') }}
        </span>
      </div>
      <div v-if="!applications.data?.length" class="text-center py-20 text-slate-400">
        <p class="font-medium">No applications yet</p>
        <Link :href="route('jobs.index')" class="text-bd-pink-500 hover:underline text-sm mt-2 inline-block">Browse Jobs →</Link>
      </div>
    </div>
  </SeekerLayout>
</template>