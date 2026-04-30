<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'
import AppLayout from '@/Layouts/AppLayout.vue'
import JobCard from '@/Components/Shared/JobCard.vue'
import Pagination from '@/Components/Shared/Pagination.vue'

const props = defineProps({
  jobs: Object,
  filters: Object,
  categories: Array,
  meta: Object,
})

const localFilters = ref({ ...props.filters })
const filtersOpen  = ref(false)

const applyFilters = debounce(() => {
  router.get(route('jobs.index'), localFilters.value, {
    preserveState: true, preserveScroll: true, replace: true,
  })
}, 400)

import { watch } from 'vue'
watch(localFilters, applyFilters, { deep: true })

function clearFilters() {
  localFilters.value = {}
}

const hasActiveFilters = computed(() =>
  Object.values(localFilters.value).some(v => v !== '' && v !== null && v !== undefined)
)

const JOB_TYPES = [
  { value: 'full_time',  label: 'Full Time'  },
  { value: 'part_time',  label: 'Part Time'  },
  { value: 'contract',   label: 'Contract'   },
  { value: 'internship', label: 'Internship' },
  { value: 'remote',     label: 'Remote'     },
  { value: 'hybrid',     label: 'Hybrid'     },
]

const EXPERIENCE_LEVELS = [
  { value: 'entry',     label: 'Entry Level'       },
  { value: 'junior',    label: 'Junior (1-2 yrs)'  },
  { value: 'mid',       label: 'Mid Level (3-5 yrs)'},
  { value: 'senior',    label: 'Senior (5+ yrs)'   },
  { value: 'lead',      label: 'Lead / Manager'    },
  { value: 'executive', label: 'Executive / C-Suite'},
]
</script>

<template>
  <AppLayout title="Browse Jobs">

    <!-- Hero Search Bar -->
    <section class="bg-gradient-to-r from-slate-800 to-slate-900 py-8 md:py-10">
      <div class="max-w-5xl mx-auto px-4">
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">Find Your Dream Job</h1>
        <p class="text-slate-400 mb-5 text-sm md:text-base">
          {{ meta.total.toLocaleString() }} jobs available right now
        </p>
        <div class="flex flex-col sm:flex-row gap-3">
          <input v-model="localFilters.q" type="text" placeholder="Job title, skill, or company…"
            class="flex-1 px-4 py-3 rounded-lg bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
          <input v-model="localFilters.location" type="text" placeholder="District / City"
            class="sm:w-44 px-4 py-3 rounded-lg bg-white text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500" />
        </div>
      </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 py-6 md:py-8">

      <!-- Mobile filter toggle -->
      <div class="flex items-center justify-between mb-4 md:hidden">
        <p class="text-sm text-slate-500">
          <span class="font-medium text-slate-700">{{ jobs.meta.total }}</span> jobs
        </p>
        <button @click="filtersOpen = !filtersOpen"
          class="flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 12h10M11 20h2"/>
          </svg>
          Filters
          <span v-if="hasActiveFilters" class="w-2 h-2 rounded-full bg-emerald-500" />
        </button>
      </div>

      <div class="flex flex-col md:flex-row gap-6 md:gap-8">

        <!-- Sidebar Filters -->
        <aside class="md:w-64 flex-shrink-0 space-y-6"
          :class="filtersOpen ? 'block' : 'hidden md:block'">
          <div class="flex items-center justify-between">
            <h2 class="font-semibold text-slate-700">Filters</h2>
            <button v-if="hasActiveFilters" @click="clearFilters" class="text-xs text-emerald-600 hover:underline">Clear all</button>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Category</h3>
            <ul class="space-y-1">
              <li v-for="cat in categories" :key="cat.id">
                <button @click="localFilters.category = localFilters.category === cat.slug ? null : cat.slug"
                  class="w-full flex items-center justify-between px-3 py-1.5 rounded-md text-sm transition-colors"
                  :class="localFilters.category === cat.slug ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-slate-600 hover:bg-slate-50'">
                  <span>{{ cat.name }}</span>
                  <span class="text-xs text-slate-400">{{ cat.job_count }}</span>
                </button>
              </li>
            </ul>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Job Type</h3>
            <div class="space-y-1">
              <label v-for="type in JOB_TYPES" :key="type.value" class="flex items-center gap-2 cursor-pointer">
                <input type="radio" :value="type.value" v-model="localFilters.job_type" class="accent-emerald-500" />
                <span class="text-sm text-slate-600">{{ type.label }}</span>
              </label>
            </div>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Salary (BDT/mo)</h3>
            <div class="flex gap-2">
              <input v-model.number="localFilters.salary_min" type="number" placeholder="Min"
                class="w-full px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400" />
              <input v-model.number="localFilters.salary_max" type="number" placeholder="Max"
                class="w-full px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <div>
            <h3 class="text-sm font-semibold text-slate-600 uppercase tracking-wide mb-2">Experience</h3>
            <div class="space-y-1">
              <label v-for="exp in EXPERIENCE_LEVELS" :key="exp.value" class="flex items-center gap-2 cursor-pointer">
                <input type="radio" :value="exp.value" v-model="localFilters.experience" class="accent-emerald-500" />
                <span class="text-sm text-slate-600">{{ exp.label }}</span>
              </label>
            </div>
          </div>

          <!-- Close filters on mobile -->
          <button @click="filtersOpen = false"
            class="md:hidden w-full py-2 bg-emerald-500 text-white text-sm font-semibold rounded-lg">
            Apply Filters
          </button>
        </aside>

        <!-- Job Listings -->
        <main class="flex-1 min-w-0">
          <div class="flex items-center justify-between mb-4">
            <p class="text-sm text-slate-500 hidden md:block">
              Showing <span class="font-medium text-slate-700">{{ jobs.meta.total }}</span> jobs
            </p>
            <select v-model="localFilters.sort"
              class="px-3 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-emerald-400 ml-auto">
              <option value="latest">Latest First</option>
              <option value="salary_high">Salary: High to Low</option>
              <option value="salary_low">Salary: Low to High</option>
              <option value="deadline">Closing Soon</option>
            </select>
          </div>

          <div v-if="jobs.data.length === 0" class="text-center py-20 text-slate-400">
            <svg class="mx-auto w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="font-medium">No jobs found</p>
            <p class="text-sm mt-1">Try adjusting your search filters</p>
          </div>

          <div v-else class="space-y-4">
            <JobCard v-for="job in jobs.data" :key="job.id" :job="job" />
          </div>

          <div class="mt-8">
            <Pagination :links="jobs.meta.links" />
          </div>
        </main>
      </div>
    </div>
  </AppLayout>
</template>