<script setup>
// Pages/JobSeeker/Dashboard.vue
// ─────────────────────────────────────────────────────────────────────────────

import { Link } from '@inertiajs/vue3'
import SeekerLayout from '@/Layouts/SeekerLayout.vue'

const props = defineProps({
  stats: Object,           // { applications, saved, views, profile_views }
  recentApplications: Array,
  recommendedJobs: Array,
})

const STATUS_COLORS = {
  pending:     'bg-yellow-100 text-yellow-700',
  reviewed:    'bg-blue-100 text-blue-700',
  shortlisted: 'bg-indigo-100 text-indigo-700',
  interview:   'bg-purple-100 text-purple-700',
  offered:     'bg-amber-100 text-amber-700',
  hired:       'bg-emerald-100 text-emerald-700',
  rejected:    'bg-red-100 text-red-500',
  withdrawn:   'bg-slate-100 text-slate-400',
}
</script>

<template>
  <SeekerLayout title="Dashboard">

    <!-- Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Applications</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.applications }}</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Saved Jobs</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.saved }}</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Profile Views</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.profile_views ?? 0 }}</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Interviews</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.interviews ?? 0 }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

      <!-- Recent Applications -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
          <h3 class="font-semibold text-slate-800">Recent Applications</h3>
          <Link :href="route('seeker.applications.index')" class="text-xs text-emerald-600 hover:underline">
            View all →
          </Link>
        </div>
        <div class="space-y-3">
          <div v-for="app in recentApplications" :key="app.id"
            class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-50 transition-colors">
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-400 flex-shrink-0">
              {{ app.job_post?.employer_profile?.company_name?.charAt(0) ?? '?' }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-slate-800 truncate">{{ app.job_post?.title }}</p>
              <p class="text-xs text-slate-400">{{ app.job_post?.employer_profile?.company_name }}</p>
            </div>
            <span class="px-2 py-0.5 text-xs font-medium rounded-full flex-shrink-0"
              :class="STATUS_COLORS[app.status] ?? 'bg-slate-100 text-slate-500'">
              {{ app.status }}
            </span>
          </div>
          <div v-if="!recentApplications?.length" class="text-center py-8 text-slate-300 text-sm">
            No applications yet.
            <Link :href="route('jobs.index')" class="text-emerald-500 hover:underline">Browse jobs →</Link>
          </div>
        </div>
      </div>

      <!-- Recommended Jobs (from search logs) -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
        <h3 class="font-semibold text-slate-800 mb-5">Recommended For You</h3>
        <div class="space-y-3">
          <Link
            v-for="job in recommendedJobs" :key="job.id"
            :href="route('jobs.show', { job: job.slug })"
            class="block p-3 rounded-xl hover:bg-slate-50 transition-colors group"
          >
            <p class="text-sm font-medium text-slate-800 group-hover:text-emerald-700 transition-colors">{{ job.title }}</p>
            <div class="flex items-center justify-between mt-1">
              <p class="text-xs text-slate-400">{{ job.employer?.name }} · {{ job.district }}</p>
              <span class="text-xs text-emerald-600 font-medium">{{ job.salary_display }}</span>
            </div>
          </Link>
          <div v-if="!recommendedJobs?.length" class="text-center py-8 text-slate-300 text-sm">
            Start browsing to get recommendations
          </div>
        </div>
      </div>

    </div>
  </SeekerLayout>
</template>