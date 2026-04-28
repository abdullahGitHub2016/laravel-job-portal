<script setup>
// Pages/Employer/Applicants/Show.vue
// ─────────────────────────────────────────────────────────────────────────────
// Full applicant profile view for employers. Shows cover letter, resume data,
// work experience, education, skills, and a status-update panel.
// ─────────────────────────────────────────────────────────────────────────────

import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({
  job:         Object,
  application: Object,
})

const seeker   = props.application.job_seeker_profile
const user     = seeker?.user

// ── Status update ─────────────────────────────────────────────────────────────
const STATUSES = [
  { value: 'reviewed',    label: 'Reviewed',    color: 'bg-blue-100 text-blue-700'     },
  { value: 'shortlisted', label: 'Shortlisted', color: 'bg-indigo-100 text-indigo-700' },
  { value: 'interview',   label: 'Interview',   color: 'bg-purple-100 text-purple-700' },
  { value: 'offered',     label: 'Offered',     color: 'bg-amber-100 text-amber-700'   },
  { value: 'hired',       label: 'Hired',       color: 'bg-emerald-100 text-emerald-700'},
  { value: 'rejected',    label: 'Rejected',    color: 'bg-red-100 text-red-500'       },
]

const STATUS_COLOR = Object.fromEntries(STATUSES.map(s => [s.value, s.color]))
STATUS_COLOR.pending = 'bg-yellow-100 text-yellow-700'

const currentStatus = ref(props.application.status)
const notes         = ref(props.application.employer_notes ?? '')
const saving        = ref(false)

function updateStatus(newStatus) {
  saving.value = true
  router.patch(
    route('employer.applicants.status', { job: props.job.id, application: props.application.id }),
    { status: newStatus, notes: notes.value },
    {
      preserveScroll: true,
      onSuccess: () => { currentStatus.value = newStatus },
      onFinish:  () => { saving.value = false },
    }
  )
}

function saveNotes() {
  saving.value = true
  router.patch(
    route('employer.applicants.status', { job: props.job.id, application: props.application.id }),
    { status: currentStatus.value, notes: notes.value },
    {
      preserveScroll: true,
      onFinish: () => { saving.value = false },
    }
  )
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function formatDate(date) {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('en-GB', { month: 'short', year: 'numeric' })
}
</script>

<template>
  <EmployerLayout :title="`${user?.name ?? 'Applicant'} — ${job.title}`">

    <!-- Breadcrumb -->
    <div class="mb-6 flex items-center gap-2 text-sm text-slate-500">
      <Link :href="route('employer.applicants.index', { job: job.id })"
            class="hover:text-emerald-600 transition-colors">
        ← Back to {{ job.title }}
      </Link>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">

      <!-- ── Left: Applicant Profile ──────────────────────────────────────── -->
      <div class="flex-1 min-w-0 space-y-5">

        <!-- Header Card -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <div class="flex items-start gap-5">
            <div class="w-16 h-16 rounded-full bg-emerald-100 flex items-center justify-center text-2xl font-bold text-emerald-700 flex-shrink-0">
              {{ user?.name?.charAt(0) ?? '?' }}
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-xl font-bold text-slate-900">{{ user?.name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                      :class="STATUS_COLOR[currentStatus] ?? 'bg-slate-100 text-slate-500'">
                  {{ currentStatus }}
                </span>
              </div>
              <p class="text-slate-600 mt-0.5">{{ seeker?.current_job_title ?? 'Job Seeker' }}</p>
              <div class="flex flex-wrap gap-x-4 gap-y-1 mt-2 text-sm text-slate-500">
                <span v-if="user?.email">✉ {{ user.email }}</span>
                <span v-if="user?.phone">📞 {{ user.phone }}</span>
                <span v-if="seeker?.district">📍 {{ seeker.district }}</span>
                <span v-if="seeker?.years_of_experience">{{ seeker.years_of_experience }} yrs exp</span>
              </div>
            </div>
            <!-- Resume download -->
            <a v-if="application.resume_snapshot"
               :href="route('employer.applicants.resume', { application: application.id })"
               class="flex-shrink-0 px-4 py-2 border border-slate-200 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-colors">
              ⬇ Resume
            </a>
          </div>

          <!-- Seeker meta chips -->
          <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-slate-100">
            <span v-if="seeker?.preferred_job_type"
                  class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">
              {{ seeker.preferred_job_type.replace('_', ' ') }}
            </span>
            <span v-if="seeker?.job_seeking_status"
                  class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-medium">
              {{ seeker.job_seeking_status.replace('_', ' ') }}
            </span>
            <span v-if="seeker?.expected_salary_min || seeker?.expected_salary_max"
                  class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
              Expected: ৳{{ Number(seeker.expected_salary_min ?? 0).toLocaleString() }}
              <template v-if="seeker.expected_salary_max"> – ৳{{ Number(seeker.expected_salary_max).toLocaleString() }}</template>
            </span>
            <span v-if="application.expected_salary"
                  class="px-2.5 py-1 bg-indigo-50 text-indigo-700 rounded-full text-xs font-medium">
              Applied salary: ৳{{ Number(application.expected_salary).toLocaleString() }}
            </span>
          </div>
        </div>

        <!-- Cover Letter -->
        <div v-if="application.cover_letter" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <h3 class="font-semibold text-slate-800 mb-3">Cover Letter</h3>
          <p class="text-slate-600 text-sm leading-relaxed whitespace-pre-line">{{ application.cover_letter }}</p>
        </div>

        <!-- Bio -->
        <div v-if="seeker?.bio" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <h3 class="font-semibold text-slate-800 mb-3">About</h3>
          <p class="text-slate-600 text-sm leading-relaxed">{{ seeker.bio }}</p>
        </div>

        <!-- Work Experience -->
        <div v-if="seeker?.work_experiences?.length" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <h3 class="font-semibold text-slate-800 mb-4">Work Experience</h3>
          <div class="space-y-4">
            <div v-for="exp in seeker.work_experiences" :key="exp.id"
                 class="flex gap-4">
              <div class="w-2 flex flex-col items-center">
                <div class="w-2 h-2 rounded-full bg-emerald-400 mt-1.5 flex-shrink-0"></div>
                <div class="flex-1 w-px bg-slate-100 mt-1"></div>
              </div>
              <div class="flex-1 pb-4">
                <div class="flex items-start justify-between gap-2">
                  <div>
                    <p class="font-semibold text-slate-800 text-sm">{{ exp.job_title }}</p>
                    <p class="text-slate-600 text-sm">{{ exp.company_name }}</p>
                  </div>
                  <span class="text-xs text-slate-400 flex-shrink-0">
                    {{ formatDate(exp.start_date) }} – {{ exp.is_current ? 'Present' : formatDate(exp.end_date) }}
                  </span>
                </div>
                <p v-if="exp.location" class="text-xs text-slate-400 mt-0.5">{{ exp.location }}</p>
                <p v-if="exp.responsibilities" class="text-slate-500 text-xs mt-2 leading-relaxed">
                  {{ exp.responsibilities }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Education -->
        <div v-if="seeker?.educations?.length" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <h3 class="font-semibold text-slate-800 mb-4">Education</h3>
          <div class="space-y-3">
            <div v-for="edu in seeker.educations" :key="edu.id"
                 class="flex items-start gap-3 p-3 rounded-xl bg-slate-50">
              <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 text-sm">🎓</div>
              <div>
                <p class="font-medium text-slate-800 text-sm">{{ edu.degree }}
                  <span v-if="edu.field_of_study" class="font-normal text-slate-500"> in {{ edu.field_of_study }}</span>
                </p>
                <p class="text-sm text-slate-600">{{ edu.institution_name }}</p>
                <div class="flex gap-3 mt-0.5 text-xs text-slate-400">
                  <span v-if="edu.passing_year">{{ edu.passing_year }}</span>
                  <span v-if="edu.result">{{ edu.result }}: {{ edu.result_value }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Skills -->
        <div v-if="seeker?.skills?.length" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
          <h3 class="font-semibold text-slate-800 mb-3">Skills</h3>
          <div class="flex flex-wrap gap-2">
            <span v-for="skill in seeker.skills" :key="skill.id"
                  class="px-3 py-1 bg-slate-100 text-slate-700 rounded-full text-sm font-medium">
              {{ skill.name }}
              <span v-if="skill.pivot?.proficiency"
                    class="text-xs text-slate-400 ml-1">{{ skill.pivot.proficiency }}</span>
            </span>
          </div>
        </div>

      </div>

      <!-- ── Right: Action Sidebar ─────────────────────────────────────────── -->
      <div class="lg:w-72 flex-shrink-0">
        <div class="sticky top-6 space-y-4">

          <!-- Status Update -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-4">Update Status</h3>
            <div class="space-y-2">
              <button
                v-for="s in STATUSES" :key="s.value"
                @click="updateStatus(s.value)"
                :disabled="saving || currentStatus === s.value"
                class="w-full flex items-center justify-between px-3 py-2 rounded-lg border text-sm font-medium transition-colors"
                :class="currentStatus === s.value
                  ? s.color + ' border-transparent cursor-default'
                  : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
              >
                {{ s.label }}
                <span v-if="currentStatus === s.value" class="text-xs opacity-60">current</span>
              </button>
            </div>
          </div>

          <!-- Notes -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-3">Employer Notes</h3>
            <textarea
              v-model="notes"
              rows="4"
              placeholder="Private notes about this applicant…"
              class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm resize-none
                     focus:outline-none focus:border-emerald-400"
            />
            <button
              @click="saveNotes"
              :disabled="saving"
              class="mt-2 w-full px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm
                     font-medium rounded-lg transition-colors disabled:opacity-50"
            >
              {{ saving ? 'Saving…' : 'Save Notes' }}
            </button>
          </div>

          <!-- Application Meta -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5">
            <h3 class="font-semibold text-slate-800 mb-3">Application Info</h3>
            <dl class="space-y-2 text-sm">
              <div class="flex justify-between">
                <dt class="text-slate-500">Applied</dt>
                <dd class="text-slate-700 font-medium">{{ formatDate(application.created_at) }}</dd>
              </div>
              <div class="flex justify-between">
                <dt class="text-slate-500">Reviewed</dt>
                <dd class="text-slate-700 font-medium">{{ application.reviewed_at ? formatDate(application.reviewed_at) : '—' }}</dd>
              </div>
            </dl>
          </div>

        </div>
      </div>

    </div>
  </EmployerLayout>
</template>