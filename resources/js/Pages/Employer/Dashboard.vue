<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({ stats: Object, recentJobs: Array })

const page = usePage()
const user = computed(() => page.props.auth.user)

const STATUS_STYLES = {
  published: { dot: 'bg-emerald-400', text: 'text-emerald-600', bg: 'bg-emerald-50' },
  draft:     { dot: 'bg-slate-300',   text: 'text-slate-500',   bg: 'bg-slate-50'   },
  paused:    { dot: 'bg-amber-400',   text: 'text-amber-600',   bg: 'bg-amber-50'   },
  closed:    { dot: 'bg-red-300',     text: 'text-red-500',     bg: 'bg-red-50'     },
}
</script>

<template>
  <EmployerLayout title="Dashboard">

    <!-- Welcome Banner -->
    <div class="mb-6 md:mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h2 class="text-xl md:text-2xl font-bold text-slate-900">
          Welcome back, {{ user?.name?.split(' ')[0] ?? 'there' }} 👋
        </h2>
        <p class="text-sm text-slate-500 mt-0.5">Here's what's happening with your jobs today.</p>
      </div>
      <Link :href="route('employer.jobs.create')"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600
               text-white text-sm font-semibold rounded-xl transition-colors shadow-sm shadow-emerald-200 self-start sm:self-auto">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Post New Job
      </Link>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 md:mb-8">

      <!-- Total Jobs -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
          </svg>
        </div>
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Jobs</p>
          <p class="text-3xl font-bold text-slate-900 mt-0.5 leading-none">{{ stats.total_jobs }}</p>
        </div>
      </div>

      <!-- Active Jobs -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Active Jobs</p>
          <p class="text-3xl font-bold text-emerald-600 mt-0.5 leading-none">{{ stats.active_jobs }}</p>
        </div>
      </div>

      <!-- Total Applicants -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
          <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
          </svg>
        </div>
        <div>
          <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Applicants</p>
          <p class="text-3xl font-bold text-slate-900 mt-0.5 leading-none">{{ stats.total_applicants }}</p>
        </div>
      </div>

    </div>

    <!-- Recent Job Posts -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
      <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-800">Recent Job Posts</h3>
        <Link :href="route('employer.jobs.index')"
          class="text-xs text-emerald-600 hover:text-emerald-700 font-medium hover:underline">
          View all →
        </Link>
      </div>

      <!-- Job rows -->
      <div class="divide-y divide-slate-50">
        <div v-for="job in recentJobs" :key="job.id"
          class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 px-5 md:px-6 py-4 hover:bg-slate-50 transition-colors">

          <!-- Icon + title -->
          <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
              <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
              </svg>
            </div>
            <div class="min-w-0">
              <p class="font-medium text-slate-800 text-sm truncate">{{ job.title }}</p>
              <p class="text-xs text-slate-400 mt-0.5">
                {{ job.applications_count }} applicant{{ job.applications_count !== 1 ? 's' : '' }}
              </p>
            </div>
          </div>

          <!-- Status badge + action -->
          <div class="flex items-center justify-between sm:justify-end gap-3 ml-12 sm:ml-0">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
              :class="(STATUS_STYLES[job.status] ?? STATUS_STYLES.draft).bg + ' ' + (STATUS_STYLES[job.status] ?? STATUS_STYLES.draft).text">
              <span class="w-1.5 h-1.5 rounded-full"
                :class="(STATUS_STYLES[job.status] ?? STATUS_STYLES.draft).dot" />
              {{ job.status }}
            </span>
            <Link :href="route('employer.applicants.index', job.id)"
              class="text-xs font-medium text-emerald-600 hover:text-emerald-700 whitespace-nowrap hover:underline">
              View Applicants →
            </Link>
          </div>
        </div>

        <!-- Empty state -->
        <div v-if="!recentJobs?.length" class="px-6 py-14 text-center">
          <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
            <svg class="w-7 h-7 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M20 7H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2zM16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/>
            </svg>
          </div>
          <p class="font-medium text-slate-600 text-sm">No job posts yet</p>
          <p class="text-slate-400 text-xs mt-1 mb-4">Start attracting candidates today</p>
          <Link :href="route('employer.jobs.create')"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors">
            Post your first job →
          </Link>
        </div>
      </div>
    </div>

  </EmployerLayout>
</template>