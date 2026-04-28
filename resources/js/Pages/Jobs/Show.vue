<script setup>
// Pages/Jobs/Show.vue
// ─────────────────────────────────────────────────────────────────────────────
// Job detail page. Handles one-click apply with optional cover letter modal.
// ─────────────────────────────────────────────────────────────────────────────

import { ref, computed } from 'vue'
import { useForm, usePage, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import JobCard from '@/Components/Shared/JobCard.vue'
import Badge from '@/Components/Shared/Badge.vue'

const props = defineProps({
  job:               Object,
  relatedJobs:       Array,
  hasApplied:        Boolean,
  applicationStatus: String,
  hasSaved:          Boolean,
})

const page = usePage()
const auth = computed(() => page.props.auth)
const isSeeker = computed(() => auth.value.user?.user_type === 'job_seeker')

// ── Apply Modal ──────────────────────────────────────────────────────────────
const showApplyModal = ref(false)

const form = useForm({
  cover_letter:    '',
  expected_salary: '',
})

function submitApplication() {
  form.post(route('seeker.applications.store', props.job.id), {
    onSuccess: () => {
      showApplyModal.value = false
      form.reset()
    },
  })
}

// ── Save / Unsave ────────────────────────────────────────────────────────────
const savedState = ref(props.hasSaved)

function toggleSave() {
  if (savedState.value) {
    useForm({}).delete(route('seeker.jobs.unsave', props.job.id), {
      preserveScroll: true,
      onSuccess: () => { savedState.value = false },
    })
  } else {
    useForm({}).post(route('seeker.jobs.save', props.job.id), {
      preserveScroll: true,
      onSuccess: () => { savedState.value = true },
    })
  }
}

// Status badge color map
const statusColors = {
  pending:     'bg-yellow-100 text-yellow-700',
  reviewed:    'bg-blue-100 text-blue-700',
  shortlisted: 'bg-indigo-100 text-indigo-700',
  interview:   'bg-purple-100 text-purple-700',
  offered:     'bg-emerald-100 text-emerald-700',
  hired:       'bg-green-100 text-green-700',
  rejected:    'bg-red-100 text-red-700',
  withdrawn:   'bg-slate-100 text-slate-500',
}
</script>

<template>
  <AppLayout :title="job.title">
    <div class="max-w-6xl mx-auto px-4 py-10">
      <div class="flex flex-col lg:flex-row gap-8">

        <!-- ── Main Content ──────────────────────────────────────────── -->
        <article class="flex-1 min-w-0">

          <!-- Header -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 mb-6">
            <div class="flex items-start gap-5">

              <!-- Company Logo -->
              <div class="w-16 h-16 rounded-xl bg-slate-100 flex items-center justify-center flex-shrink-0 overflow-hidden">
                <img v-if="job.employer?.logo" :src="job.employer.logo" :alt="job.employer.name" class="w-full h-full object-cover"/>
                <span v-else class="text-2xl font-bold text-slate-400">
                  {{ job.employer?.name?.charAt(0) }}
                </span>
              </div>

              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap gap-2 mb-2">
                  <Badge v-if="job.is_featured" variant="featured">⭐ Featured</Badge>
                  <Badge v-if="job.is_hot"      variant="hot">🔥 Hot Job</Badge>
                  <Badge v-if="job.is_urgent"   variant="urgent">⚡ Urgent</Badge>
                </div>

                <h1 class="text-2xl font-bold text-slate-900">{{ job.title }}</h1>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-slate-500 text-sm">
                  <span class="font-medium text-slate-700">{{ job.employer?.name }}</span>
                  <span v-if="job.employer?.is_verified" class="flex items-center gap-1 text-emerald-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Verified
                  </span>
                  <span>{{ job.district }}</span>
                  <span>{{ job.published_at }}</span>
                </div>

                <!-- Meta chips -->
                <div class="flex flex-wrap gap-2 mt-4">
                  <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">
                    {{ job.job_type_label }}
                  </span>
                  <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-medium">
                    {{ job.experience_label }}
                  </span>
                  <span v-if="job.salary_display" class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-xs font-semibold">
                    {{ job.salary_display }}
                  </span>
                  <span class="px-3 py-1 bg-orange-50 text-orange-700 rounded-full text-xs font-medium">
                    Deadline: {{ job.application_deadline }}
                    <template v-if="job.days_remaining !== null">
                      ({{ job.days_remaining }}d left)
                    </template>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Job Description -->
          <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-6">

            <section>
              <h2 class="text-lg font-semibold text-slate-900 mb-3">Job Description</h2>
              <div class="prose prose-slate max-w-none text-slate-600 leading-relaxed"
                   v-html="job.description" />
            </section>

            <section v-if="job.requirements">
              <h2 class="text-lg font-semibold text-slate-900 mb-3">Requirements</h2>
              <div class="prose prose-slate max-w-none text-slate-600"
                   v-html="job.requirements" />
            </section>

            <section v-if="job.benefits">
              <h2 class="text-lg font-semibold text-slate-900 mb-3">Benefits</h2>
              <div class="prose prose-slate max-w-none text-slate-600"
                   v-html="job.benefits" />
            </section>

            <!-- Required Skills -->
            <section v-if="job.skills?.length">
              <h2 class="text-lg font-semibold text-slate-900 mb-3">Required Skills</h2>
              <div class="flex flex-wrap gap-2">
                <span
                  v-for="skill in job.skills"
                  :key="skill.id"
                  class="px-3 py-1 rounded-full text-sm font-medium"
                  :class="skill.is_required
                    ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200'
                    : 'bg-slate-100 text-slate-600'"
                >
                  {{ skill.name }}
                  <span v-if="!skill.is_required" class="text-slate-400 text-xs ml-1">(optional)</span>
                </span>
              </div>
            </section>

          </div>
        </article>

        <!-- ── Sticky Sidebar ────────────────────────────────────────── -->
        <aside class="lg:w-80 flex-shrink-0">
          <div class="sticky top-6 space-y-4">

            <!-- Apply Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">

              <!-- Already applied -->
              <template v-if="hasApplied">
                <div class="text-center py-2">
                  <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                  <p class="font-semibold text-slate-800">Application Submitted</p>
                  <span
                    class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium"
                    :class="statusColors[applicationStatus] || 'bg-slate-100 text-slate-600'"
                  >
                    {{ applicationStatus?.replace('_', ' ') }}
                  </span>
                </div>
              </template>

              <!-- Not a seeker / not logged in -->
              <template v-else-if="!isSeeker">
                <Link
                  :href="route('register.seeker')"
                  class="block w-full text-center px-6 py-3 bg-emerald-500 hover:bg-emerald-600
                         text-white font-semibold rounded-xl transition-colors"
                >
                  Create Account to Apply
                </Link>
                <p class="text-center text-xs text-slate-400 mt-2">
                  Already have an account?
                  <Link :href="route('login')" class="text-emerald-600 hover:underline">Sign in</Link>
                </p>
              </template>

              <!-- Apply button for seekers -->
              <template v-else>
                <button
                  @click="showApplyModal = true"
                  class="w-full px-6 py-3 bg-emerald-500 hover:bg-emerald-600
                         text-white font-semibold rounded-xl transition-colors"
                >
                  Apply Now
                </button>

                <!-- Save button -->
                <button
                  @click="toggleSave"
                  class="w-full mt-2 px-6 py-3 border rounded-xl text-sm font-medium transition-colors flex items-center justify-center gap-2"
                  :class="savedState
                    ? 'border-emerald-200 bg-emerald-50 text-emerald-700'
                    : 'border-slate-200 text-slate-600 hover:bg-slate-50'"
                >
                  <svg class="w-4 h-4" :fill="savedState ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                  </svg>
                  {{ savedState ? 'Saved' : 'Save Job' }}
                </button>
              </template>

              <!-- Job summary -->
              <dl class="mt-5 pt-5 border-t border-slate-100 space-y-2.5 text-sm">
                <div class="flex justify-between">
                  <dt class="text-slate-500">Vacancies</dt>
                  <dd class="font-medium text-slate-700">{{ job.vacancies }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-slate-500">Experience</dt>
                  <dd class="font-medium text-slate-700">{{ job.experience_label }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-slate-500">Job Type</dt>
                  <dd class="font-medium text-slate-700">{{ job.job_type_label }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-slate-500">Salary</dt>
                  <dd class="font-medium text-emerald-700">{{ job.salary_display }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-slate-500">Deadline</dt>
                  <dd class="font-medium text-orange-600">{{ job.application_deadline }}</dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-slate-500">Views</dt>
                  <dd class="font-medium text-slate-700">{{ job.view_count.toLocaleString() }}</dd>
                </div>
              </dl>
            </div>

            <!-- Company Card -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
              <h3 class="font-semibold text-slate-800 mb-4">About the Company</h3>
              <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center">
                  <img v-if="job.employer?.logo" :src="job.employer.logo" class="w-full h-full object-cover rounded-lg"/>
                  <span v-else class="font-bold text-slate-400">{{ job.employer?.name?.charAt(0) }}</span>
                </div>
                <div>
                  <p class="font-medium text-slate-800 text-sm">{{ job.employer?.name }}</p>
                  <p class="text-xs text-slate-500">{{ job.employer?.district }}</p>
                </div>
              </div>
            </div>

          </div>
        </aside>

      </div>

      <!-- ── Related Jobs ───────────────────────────────────────────────── -->
      <div v-if="relatedJobs.length" class="mt-12">
        <h2 class="text-xl font-bold text-slate-900 mb-5">Similar Jobs</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <JobCard v-for="rJob in relatedJobs" :key="rJob.id" :job="rJob" />
        </div>
      </div>

    </div>

    <!-- ─────────────────────────────────────────────────────────────────── -->
    <!-- Apply Modal                                                          -->
    <!-- ─────────────────────────────────────────────────────────────────── -->
    <Teleport to="body">
      <Transition name="modal">
        <div
          v-if="showApplyModal"
          class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm"
          @click.self="showApplyModal = false"
        >
          <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg">

            <div class="p-6 border-b border-slate-100">
              <h3 class="text-lg font-bold text-slate-900">Apply for {{ job.title }}</h3>
              <p class="text-sm text-slate-500 mt-0.5">at {{ job.employer?.name }}</p>
            </div>

            <form @submit.prevent="submitApplication" class="p-6 space-y-4">

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                  Cover Letter <span class="text-slate-400 font-normal">(optional)</span>
                </label>
                <textarea
                  v-model="form.cover_letter"
                  rows="6"
                  placeholder="Tell the employer why you're a great fit…"
                  class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                         focus:outline-none focus:border-emerald-400 resize-none"
                />
                <p v-if="form.errors.cover_letter" class="mt-1 text-xs text-red-500">
                  {{ form.errors.cover_letter }}
                </p>
              </div>

              <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                  Expected Salary <span class="text-slate-400 font-normal">(BDT/month, optional)</span>
                </label>
                <input
                  v-model.number="form.expected_salary"
                  type="number"
                  placeholder="e.g. 50000"
                  class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm
                         focus:outline-none focus:border-emerald-400"
                />
              </div>

              <div class="flex gap-3 pt-2">
                <button
                  type="button"
                  @click="showApplyModal = false"
                  class="flex-1 px-4 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="flex-1 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600
                         text-white rounded-lg text-sm font-semibold transition-colors
                         disabled:opacity-60 disabled:cursor-not-allowed"
                >
                  <span v-if="form.processing">Submitting…</span>
                  <span v-else>Submit Application</span>
                </button>
              </div>

            </form>
          </div>
        </div>
      </Transition>
    </Teleport>

  </AppLayout>
</template>

<style scoped>
.modal-enter-active, .modal-leave-active { transition: all 0.2s ease; }
.modal-enter-from, .modal-leave-to       { opacity: 0; transform: scale(0.96); }
</style>
