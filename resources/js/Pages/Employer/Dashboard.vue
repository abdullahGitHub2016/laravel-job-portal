<script setup>
import { Link } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

defineProps({ stats: Object, recentJobs: Array })
</script>

<template>
  <EmployerLayout title="Dashboard">
    <div class="grid grid-cols-3 gap-4 mb-8">
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Jobs</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.total_jobs }}</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Active Jobs</p>
        <p class="text-3xl font-bold text-emerald-600 mt-1">{{ stats.active_jobs }}</p>
      </div>
      <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-5">
        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Applicants</p>
        <p class="text-3xl font-bold text-slate-900 mt-1">{{ stats.total_applicants }}</p>
      </div>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
      <div class="flex items-center justify-between mb-5">
        <h3 class="font-semibold text-slate-800">Recent Job Posts</h3>
        <Link :href="route('employer.jobs.create')" class="px-4 py-2 bg-emerald-500 text-white text-sm font-semibold rounded-lg hover:bg-emerald-600">+ Post Job</Link>
      </div>
      <div class="space-y-3">
        <div v-for="job in recentJobs" :key="job.id" class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-50">
          <div>
            <p class="font-medium text-slate-800 text-sm">{{ job.title }}</p>
            <p class="text-xs text-slate-400">{{ job.applications_count }} applicants · {{ job.status }}</p>
          </div>
          <Link :href="route('employer.applicants.index', job.id)" class="text-xs text-emerald-600 hover:underline">View Applicants →</Link>
        </div>
        <div v-if="!recentJobs?.length" class="text-center py-8 text-slate-400 text-sm">
          No job posts yet. <Link :href="route('employer.jobs.create')" class="text-emerald-500 hover:underline">Post your first job →</Link>
        </div>
      </div>
    </div>
  </EmployerLayout>
</template>
