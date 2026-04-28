<script setup>
// Pages/Employer/Applicants/Index.vue
// ─────────────────────────────────────────────────────────────────────────────
// Kanban-style applicant pipeline. Each column = one status stage.
// Drag-to-update is intentionally omitted (use a dedicated DnD lib like
// vue-draggable-plus for production) — status is updated via the action menu.
// ─────────────────────────────────────────────────────────────────────────────

import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({
  job:        Object,
  applicants: Object,   // Keyed by status: { pending: [...], shortlisted: [...], ... }
  statuses:   Array,
})

const STAGE_LABELS = {
  pending:     { label: 'Applied',     color: 'bg-slate-100 text-slate-600'   },
  reviewed:    { label: 'Reviewed',    color: 'bg-blue-100 text-blue-600'     },
  shortlisted: { label: 'Shortlisted', color: 'bg-indigo-100 text-indigo-700' },
  interview:   { label: 'Interview',   color: 'bg-purple-100 text-purple-700' },
  offered:     { label: 'Offered',     color: 'bg-amber-100 text-amber-700'   },
  hired:       { label: 'Hired',       color: 'bg-emerald-100 text-emerald-700'},
  rejected:    { label: 'Rejected',    color: 'bg-red-100 text-red-500'       },
}

const PIPELINE_STAGES = ['pending', 'reviewed', 'shortlisted', 'interview', 'offered', 'hired']

// ── Status Update ─────────────────────────────────────────────────────────────
const updating = ref(null)

function updateStatus(application, newStatus) {
  updating.value = application.id
  router.patch(
    route('employer.applicants.status', { job: props.job.id, application: application.id }),
    { status: newStatus },
    {
      preserveScroll: true,
      onFinish: () => { updating.value = null },
    }
  )
}

function totalCount() {
  return Object.values(props.applicants).flat().length
}
</script>

<template>
  <EmployerLayout :title="`Applicants — ${job.title}`">

    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-xl font-bold text-slate-900">{{ job.title }}</h1>
        <p class="text-sm text-slate-500 mt-0.5">
          {{ totalCount() }} total applicants · Deadline: {{ job.application_deadline }}
        </p>
      </div>
    </div>

    <!-- Kanban Board -->
    <div class="flex gap-4 overflow-x-auto pb-4">
      <div
        v-for="status in PIPELINE_STAGES"
        :key="status"
        class="flex-shrink-0 w-72"
      >
        <!-- Column Header -->
        <div class="flex items-center justify-between mb-3">
          <span
            class="px-2.5 py-1 rounded-full text-xs font-semibold"
            :class="STAGE_LABELS[status].color"
          >
            {{ STAGE_LABELS[status].label }}
          </span>
          <span class="text-xs text-slate-400 font-medium">
            {{ (applicants[status] ?? []).length }}
          </span>
        </div>

        <!-- Applicant Cards -->
        <div class="space-y-3 min-h-24">
          <div
            v-for="app in (applicants[status] ?? [])"
            :key="app.id"
            class="bg-white rounded-xl border border-slate-100 shadow-sm p-4"
          >
            <div class="flex items-start gap-3">
              <!-- Avatar -->
              <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 text-sm font-bold text-emerald-700">
                {{ app.job_seeker_profile?.user?.name?.charAt(0) ?? '?' }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-medium text-slate-800 text-sm truncate">
                  {{ app.job_seeker_profile?.user?.name }}
                </p>
                <p class="text-xs text-slate-500 truncate">
                  {{ app.job_seeker_profile?.current_job_title ?? 'Job Seeker' }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">
                  {{ app.job_seeker_profile?.years_of_experience }}y exp
                </p>
              </div>
            </div>

            <!-- Skills chips (first 3) -->
            <div class="flex flex-wrap gap-1 mt-3">
              <span
                v-for="skill in (app.job_seeker_profile?.skills ?? []).slice(0, 3)"
                :key="skill.id"
                class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-xs"
              >
                {{ skill.name }}
              </span>
              <span
                v-if="(app.job_seeker_profile?.skills ?? []).length > 3"
                class="px-1.5 py-0.5 bg-slate-100 text-slate-400 rounded text-xs"
              >
                +{{ app.job_seeker_profile.skills.length - 3 }}
              </span>
            </div>

            <!-- Action Buttons -->
            <div class="mt-3 flex gap-2">
              <a
                :href="route('employer.applicants.show', { job: job.id, application: app.id })"
                class="flex-1 text-center px-2 py-1.5 text-xs border border-slate-200
                       text-slate-600 rounded-lg hover:bg-slate-50 transition-colors"
              >
                View
              </a>
              <!-- Move to next stage -->
              <template v-if="status !== 'hired' && status !== 'rejected'">
                <button
                  @click="updateStatus(app, PIPELINE_STAGES[PIPELINE_STAGES.indexOf(status) + 1])"
                  :disabled="updating === app.id"
                  class="flex-1 px-2 py-1.5 text-xs bg-emerald-500 hover:bg-emerald-600
                         text-white rounded-lg transition-colors disabled:opacity-50"
                >
                  {{ updating === app.id ? '…' : 'Advance →' }}
                </button>
              </template>
              <!-- Reject button -->
              <button
                v-if="status !== 'hired' && status !== 'rejected'"
                @click="updateStatus(app, 'rejected')"
                :disabled="updating === app.id"
                class="px-2 py-1.5 text-xs text-red-500 hover:bg-red-50 rounded-lg transition-colors"
              >
                ✕
              </button>
            </div>

          </div>

          <!-- Empty column placeholder -->
          <div
            v-if="!(applicants[status] ?? []).length"
            class="text-center py-8 text-slate-300 text-xs border-2 border-dashed border-slate-100 rounded-xl"
          >
            No applicants here
          </div>
        </div>

      </div>

      <!-- Rejected column (end of pipeline) -->
      <div class="flex-shrink-0 w-72">
        <div class="flex items-center justify-between mb-3">
          <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-500">
            Rejected
          </span>
          <span class="text-xs text-slate-400 font-medium">
            {{ (applicants['rejected'] ?? []).length }}
          </span>
        </div>
        <div class="space-y-2 opacity-60">
          <div
            v-for="app in (applicants['rejected'] ?? [])"
            :key="app.id"
            class="bg-white rounded-xl border border-slate-100 p-3"
          >
            <p class="text-sm font-medium text-slate-700">
              {{ app.job_seeker_profile?.user?.name }}
            </p>
          </div>
        </div>
      </div>

    </div>
  </EmployerLayout>
</template>
