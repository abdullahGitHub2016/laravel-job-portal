<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({
  job:        Object,
  applicants: Object,
  statuses:   Array,
})

const STAGE_LABELS = {
  pending:     { label: 'Applied',     color: 'bg-slate-100 text-slate-600',     dot: 'bg-slate-400',   icon: '📥' },
  reviewed:    { label: 'Reviewed',    color: 'bg-blue-100 text-blue-600',       dot: 'bg-blue-400',    icon: '👁️' },
  shortlisted: { label: 'Shortlisted', color: 'bg-indigo-100 text-indigo-700',   dot: 'bg-indigo-400',  icon: '⭐' },
  interview:   { label: 'Interview',   color: 'bg-purple-100 text-purple-700',   dot: 'bg-purple-400',  icon: '🗓️' },
  offered:     { label: 'Offered',     color: 'bg-amber-100 text-amber-700',     dot: 'bg-amber-400',   icon: '📄' },
  hired:       { label: 'Hired',       color: 'bg-bd-pink-100 text-bd-pink-700', dot: 'bg-bd-pink-500', icon: '✅' },
  rejected:    { label: 'Rejected',    color: 'bg-red-100 text-red-500',         dot: 'bg-red-400',     icon: '✕'  },
}

const PIPELINE_STAGES = ['pending', 'reviewed', 'shortlisted', 'interview', 'offered', 'hired', 'rejected']

const updating  = ref(null)
const activeTab = ref('pending')

function updateStatus(application, newStatus) {
  updating.value = application.id
  router.patch(
    route('employer.applicants.status', { job: props.job.id, application: application.id }),
    { status: newStatus },
    { preserveScroll: true, onFinish: () => { updating.value = null } }
  )
}

function totalCount() {
  return Object.values(props.applicants).flat().length
}

const activeApplicants = computed(() => props.applicants[activeTab.value] ?? [])

function nextStage(status) {
  const i = PIPELINE_STAGES.indexOf(status)
  return (i >= 0 && i < PIPELINE_STAGES.length - 2) ? PIPELINE_STAGES[i + 1] : null
}
</script>

<template>
  <EmployerLayout :title="`Applicants — ${job.title}`">

    <!-- Page Header -->
    <div class="mb-5">
      <h1 class="text-base sm:text-lg font-bold text-slate-900 leading-snug truncate">{{ job.title }}</h1>
      <p class="text-xs text-slate-500 mt-0.5">
        {{ totalCount() }} applicants &middot; Deadline: {{ job.application_deadline }}
      </p>
    </div>

    <!-- ══════════════════════════════════════════
         MOBILE  (hidden md+)
    ══════════════════════════════════════════ -->
    <div class="md:hidden">

      <!-- Scrollable tab strip -->
      <div class="relative mb-4">
        <!-- fade hint on right edge so user knows it scrolls -->
        <div class="absolute right-0 top-0 bottom-0 w-8 bg-gradient-to-l from-slate-50 to-transparent z-10 pointer-events-none rounded-r-xl" />
        <div class="flex gap-2 overflow-x-auto pb-2 flex-nowrap pr-6">
          <button
            v-for="status in PIPELINE_STAGES" :key="status"
            @click="activeTab = status"
            class="flex-shrink-0 flex flex-col items-center gap-1 px-3 pt-2.5 pb-2 rounded-xl
                   text-xs font-semibold transition-all border"
            :class="activeTab === status
              ? 'bg-bd-pink-500 text-white border-bd-pink-500 shadow-sm'
              : 'bg-white border-slate-200 text-slate-600'"
          >
            <span class="text-base leading-none">{{ STAGE_LABELS[status].icon }}</span>
            <span>{{ STAGE_LABELS[status].label }}</span>
            <span class="text-[10px] opacity-70 leading-none">{{ (applicants[status] ?? []).length }}</span>
          </button>
        </div>
      </div>

      <!-- Active stage label -->
      <div class="flex items-center gap-2 mb-3">
        <span class="px-2.5 py-1 rounded-full text-xs font-semibold"
          :class="STAGE_LABELS[activeTab].color">
          {{ STAGE_LABELS[activeTab].label }}
        </span>
        <span class="text-xs text-slate-400">{{ activeApplicants.length }} people</span>
      </div>

      <!-- Cards -->
      <div class="space-y-3">
        <div v-if="!activeApplicants.length"
          class="text-center py-14 text-slate-400 text-sm bg-white rounded-2xl border-2 border-dashed border-slate-200">
          <p class="text-2xl mb-2">{{ STAGE_LABELS[activeTab].icon }}</p>
          No applicants in this stage
        </div>

        <div v-for="app in activeApplicants" :key="app.id"
          class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

          <!-- Card header -->
          <div class="flex items-center gap-3 p-4 border-b border-slate-50">
            <div class="w-11 h-11 rounded-full bg-bd-pink-100 flex items-center justify-center
                        font-bold text-bd-pink-700 text-base flex-shrink-0">
              {{ app.job_seeker_profile?.user?.name?.charAt(0) ?? '?' }}
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-semibold text-slate-800 text-sm truncate">
                {{ app.job_seeker_profile?.user?.name }}
              </p>
              <p class="text-xs text-slate-500 truncate mt-0.5">
                {{ app.job_seeker_profile?.current_job_title ?? 'Job Seeker' }}
              </p>
            </div>
            <div class="flex-shrink-0 text-right">
              <p class="text-sm font-bold text-slate-700">{{ app.job_seeker_profile?.years_of_experience ?? 0 }}</p>
              <p class="text-[10px] text-slate-400">yrs exp</p>
            </div>
          </div>

          <!-- Skills -->
          <div v-if="(app.job_seeker_profile?.skills ?? []).length" class="px-4 py-3 flex flex-wrap gap-1.5">
            <span v-for="skill in (app.job_seeker_profile?.skills ?? []).slice(0, 5)" :key="skill.id"
              class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs">
              {{ skill.name }}
            </span>
            <span v-if="(app.job_seeker_profile?.skills ?? []).length > 5"
              class="px-2.5 py-1 bg-slate-100 text-slate-400 rounded-full text-xs">
              +{{ app.job_seeker_profile.skills.length - 5 }} more
            </span>
          </div>

          <!-- Action buttons -->
          <div class="flex border-t border-slate-100">
            <a :href="route('employer.applicants.show', { job: job.id, application: app.id })"
              class="flex-1 py-3 text-center text-xs font-semibold text-slate-600
                     hover:bg-slate-50 transition-colors border-r border-slate-100">
              View Profile
            </a>
            <button
              v-if="nextStage(activeTab) && activeTab !== 'rejected'"
              @click="updateStatus(app, nextStage(activeTab))"
              :disabled="updating === app.id"
              class="flex-1 py-3 text-xs font-semibold text-bd-pink-600
                     hover:bg-bd-pink-50 transition-colors border-r border-slate-100 disabled:opacity-40">
              {{ updating === app.id ? '…' : `Move to ${STAGE_LABELS[nextStage(activeTab)]?.label}` }}
            </button>
            <button
              v-if="activeTab !== 'rejected' && activeTab !== 'hired'"
              @click="updateStatus(app, 'rejected')"
              :disabled="updating === app.id"
              class="px-4 py-3 text-xs font-semibold text-red-400 hover:bg-red-50 transition-colors disabled:opacity-40">
              Reject
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ══════════════════════════════════════════
         DESKTOP kanban (md+)
    ══════════════════════════════════════════ -->
    <div class="hidden md:flex gap-3 overflow-x-auto pb-4 -mx-1 px-1">
      <div v-for="status in PIPELINE_STAGES" :key="status"
        class="flex-shrink-0 w-60 xl:w-68">

        <!-- Column header -->
        <div class="flex items-center justify-between mb-2.5 px-1">
          <span class="px-2.5 py-1 rounded-full text-xs font-semibold flex items-center gap-1.5"
            :class="STAGE_LABELS[status].color">
            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="STAGE_LABELS[status].dot" />
            {{ STAGE_LABELS[status].label }}
          </span>
          <span class="text-xs font-bold text-slate-400 bg-slate-100 rounded-full w-5 h-5 flex items-center justify-center">
            {{ (applicants[status] ?? []).length }}
          </span>
        </div>

        <!-- Cards column -->
        <div class="space-y-2.5 min-h-20" :class="status === 'rejected' ? 'opacity-60' : ''">

          <div v-for="app in (applicants[status] ?? [])" :key="app.id"
            class="bg-white rounded-xl border border-slate-100 shadow-sm p-3 hover:shadow-md transition-shadow">

            <!-- Name row -->
            <div class="flex items-start gap-2 mb-2">
              <div class="w-8 h-8 rounded-full bg-bd-pink-100 flex items-center justify-center
                          flex-shrink-0 text-xs font-bold text-bd-pink-700">
                {{ app.job_seeker_profile?.user?.name?.charAt(0) ?? '?' }}
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-semibold text-slate-800 text-xs truncate">
                  {{ app.job_seeker_profile?.user?.name }}
                </p>
                <p class="text-xs text-slate-500 truncate">
                  {{ app.job_seeker_profile?.current_job_title ?? 'Job Seeker' }}
                </p>
                <p class="text-[10px] text-slate-400 mt-0.5">
                  {{ app.job_seeker_profile?.years_of_experience ?? 0 }} yrs exp
                </p>
              </div>
            </div>

            <!-- Skills -->
            <div class="flex flex-wrap gap-1 mb-2.5">
              <span v-for="skill in (app.job_seeker_profile?.skills ?? []).slice(0, 3)" :key="skill.id"
                class="px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px]">
                {{ skill.name }}
              </span>
              <span v-if="(app.job_seeker_profile?.skills ?? []).length > 3"
                class="px-1.5 py-0.5 bg-slate-100 text-slate-400 rounded text-[10px]">
                +{{ app.job_seeker_profile.skills.length - 3 }}
              </span>
            </div>

            <!-- Actions -->
            <div v-if="status !== 'rejected'" class="flex gap-1.5 pt-2 border-t border-slate-50">
              <a :href="route('employer.applicants.show', { job: job.id, application: app.id })"
                class="flex-1 text-center py-1.5 text-[11px] font-medium border border-slate-200
                       text-slate-600 rounded-lg hover:bg-slate-50 transition-colors">
                View
              </a>
              <button v-if="nextStage(status)"
                @click="updateStatus(app, nextStage(status))"
                :disabled="updating === app.id"
                class="flex-1 py-1.5 text-[11px] font-medium bg-bd-pink-500 hover:bg-bd-pink-600
                       text-white rounded-lg transition-colors disabled:opacity-50">
                {{ updating === app.id ? '…' : 'Advance →' }}
              </button>
              <button @click="updateStatus(app, 'rejected')"
                :disabled="updating === app.id"
                class="w-7 flex items-center justify-center text-red-400
                       hover:bg-red-50 rounded-lg transition-colors text-xs disabled:opacity-40">
                ✕
              </button>
            </div>
          </div>

          <!-- Empty column -->
          <div v-if="!(applicants[status] ?? []).length"
            class="text-center py-8 text-slate-300 text-xs border-2 border-dashed border-slate-100 rounded-xl">
            Empty
          </div>
        </div>
      </div>
    </div>

  </EmployerLayout>
</template>