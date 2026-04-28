<script setup>
// Pages/Jobs/Index.vue
// ─────────────────────────────────────────────────────────────────────────────
// Public job listing page with live filtering via Inertia visits.
// ─────────────────────────────────────────────────────────────────────────────

import { ref, computed, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/AppLayout.vue'
import JobCard from '@/Components/Shared/JobCard.vue'
import Pagination from '@/Components/Shared/Pagination.vue'
import Badge from '@/Components/Shared/Badge.vue'

// ── Props from Inertia (set by JobController::index) ──────────────────────────
const props = defineProps({
  jobs: Object,       // JobCollection (paginated)  { data: [], links, meta }
  filters: Object,    // Current active filter state
  categories: Array,  // For sidebar
  meta: Object,       // { total, currentPage }
})

// ── Local reactive filter state (mirrors URL params) ──────────────────────────
const localFilters = ref({ ...props.filters })

// Debounced Inertia visit — updates URL and re-fetches without full page reload
const applyFilters = debounce(() => {
  router.get(route('jobs.index'), localFilters.value, {
    preserveState:  true,
    preserveScroll: true,
    replace:        true,  // Don't pollute browser history on every keystroke
  })
}, 400)

watch(localFilters, applyFilters, { deep: true })

// Reset all filters
function clearFilters() {
  localFilters.value = {}
}

const hasActiveFilters = computed(() =>
  Object.values(localFilters.value).some(v => v !== '' && v !== null && v !== undefined)
)

const JOB_TYPES = [
  { value: 'full_time',  label: 'Full Time' },
  { value: 'part_time',  label: 'Part Time' },
  { value: 'contract',   label: 'Contract' },
  { value: 'internship', label: 'Internship' },
  { value: 'remote',     label: 'Remote' },
  { value: 'hybrid',     label: 'Hybrid' },
]

const EXPERIENCE_LEVELS = [
  { value: 'entry',      label: 'Entry Level' },
  { value: 'junior',     label: 'Junior (1-2 yrs)' },
  { value: 'mid',        label: 'Mid Level (3-5 yrs)' },
  { value: 'senior',     label: 'Senior (5+ yrs)' },
  { value: 'lead',       label: 'Lead / Manager' },
  { value: 'executive',  label: 'Executive / C-Suite' },
]
</script>

<template>
  <AppLayout title="Browse Jobs">

    <!-- ── Hero Search Bar ──────────────────────────────────────────────── -->
    <section class="bg-gradient-to-r from-slate-800 to-slate-900 py-10">
      <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-white mb-2">Find Your Dream Job</h1>
        <p class="text-slate-400 mb-6">
          {{ meta.total.toLocaleString() }} jobs available right now
        </p>

        <div class="flex gap-3">
          <!-- Keyword search -->
          <input
            v-model="localFilters.q"
            type="text"
            placeholder="Job title, skill, or company…"
            class="flex-1 px-4 py-3 rounded-lg bg-white text-slate-900 placeholder-slate-400
                   focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
          <!-- Location -->
          <input
            v-model="localFilters.location"
            type="text"
            placeholder="District / City"
            class="w-48 px-4 py-3 rounded-lg bg-white text-slate-900 placeholder-slate-400
                   focus:outline-none focus:ring-2 focus:ring-emerald-500"
          />
          <button
            class="px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold
                   rounded-lg transition-colors"
          >
            Search
          </button>
        </div>
      </div>
    </section>

    <!-- ── Main Layout ───────────────────────────────────────────────────── -->
    <div class="max-w-7xl mx-auto px-4 py-8 flex gap-8">

      <!-- ── Sidebar Filters ──────────────────────────────────────────── -->
      <aside class="w-64 flex-shrink-0 space-y-6">

        <!-- Clear filters -->
        <div class="flex items-center justify-between">
          <h2 class="font-semibold text-slate-700">Filters</h2>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="text-xs text-emerald-600 hover:underline"
          >
            Clear all
          </button>
        </div>

        <!-- Category -->
        <div>
          <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Category</h3>
          <ul class="space-y-1">
            <li v-for="cat in categories" :key="cat.id">
              <button
                @click="localFilters.category = localFilters.category === cat.slug ? null : cat.slug"
                class="w-full flex items-center justify-between px-3 py-1.5 rounded-md text-sm transition-colors"
                :class="localFilters.category === cat.slug
                  ? 'bg-emerald-50 text-emerald-700 font-medium'
                  : 'text-slate-600 hover:bg-slate-50'"
              >
                <span>{{ cat.name }}</span>
                <span class="text-xs text-slate-400">{{ cat.job_count }}</span>
              </button>
            </li>
          </ul>
        </div>

        <!-- Job Type -->
        <div>
          <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Job Type</h3>
          <div class="space-y-1">
            <label v-for="type in JOB_TYPES" :key="type.value" class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                :value="type.value"
                v-model="localFilters.job_type"
                class="accent-emerald-500"
              />
              <span class="text-sm text-slate-600">{{ type.label }}</span>
            </label>
          </div>
        </div>

        <!-- Salary Range -->
        <div>
          <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Salary (BDT/mo)</h3>
          <div class="flex gap-2">
            <input
              v-model.number="localFilters.salary_min"
              type="number"
              placeholder="Min"
              class="w-full px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400"
            />
            <input
              v-model.number="localFilters.salary_max"
              type="number"
              placeholder="Max"
              class="w-full px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400"
            />
          </div>
        </div>

        <!-- Experience Level -->
        <div>
          <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Experience</h3>
          <div class="space-y-1">
            <label v-for="exp in EXPERIENCE_LEVELS" :key="exp.value" class="flex items-center gap-2 cursor-pointer">
              <input
                type="radio"
                :value="exp.value"
                v-model="localFilters.experience"
                class="accent-emerald-500"
              />
              <span class="text-sm text-slate-600">{{ exp.label }}</span>
            </label>
          </div>
        </div>

      </aside>

      <!-- ── Job Listings ─────────────────────────────────────────────── -->
      <main class="flex-1 min-w-0">

        <!-- Sort bar -->
        <div class="flex items-center justify-between mb-4">
          <p class="text-sm text-slate-500">
            Showing <span class="font-medium text-slate-700">{{ jobs.meta.total }}</span> jobs
          </p>
          <select
            v-model="localFilters.sort"
            class="px-3 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400"
          >
            <option value="latest">Latest First</option>
            <option value="salary_high">Salary: High to Low</option>
            <option value="salary_low">Salary: Low to High</option>
            <option value="deadline">Closing Soon</option>
          </select>
        </div>

        <!-- Loading state (Inertia progress bar handles it globally,
             but this gives per-column feedback) -->
        <div v-if="jobs.data.length === 0" class="text-center py-20 text-slate-400">
          <svg class="mx-auto w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
              d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <p class="font-medium">No jobs found</p>
          <p class="text-sm mt-1">Try adjusting your search filters</p>
        </div>

        <div v-else class="space-y-4">
          <JobCard
            v-for="job in jobs.data"
            :key="job.id"
            :job="job"
          />
        </div>

        <!-- Pagination -->
        <div class="mt-8">
          <Pagination :links="jobs.meta.links" />
        </div>

      </main>
    </div>

  </AppLayout>
</template>
