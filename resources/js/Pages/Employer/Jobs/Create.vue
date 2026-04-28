<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({
  categories: Array,
  skills:     Array,
  job:        { type: Object, default: null },
})

const isEditing = computed(() => !!props.job)

// ── Form State ────────────────────────────────────────────────────────────────
const form = useForm({
  title:                props.job?.title                ?? '',
  description:          props.job?.description          ?? '',
  requirements:         props.job?.requirements         ?? '',
  benefits:             props.job?.benefits             ?? '',
  category_id:          props.job?.category?.id         ?? '',
  job_type:             props.job?.job_type             ?? 'full_time',
  experience_level:     props.job?.experience_level     ?? 'mid',
  experience_years_min: props.job?.experience_years_min ?? 0,
  vacancies:            props.job?.vacancies            ?? 1,
  salary_type:          props.job?.salary_type          ?? 'monthly',
  salary_min:           props.job?.salary_min           ?? null,
  salary_max:           props.job?.salary_max           ?? null,
  currency:             props.job?.currency             ?? 'BDT',
  show_salary:          props.job?.show_salary          ?? true,
  is_remote:            props.job?.is_remote            ?? false,
  location:             props.job?.location             ?? '',
  district:             props.job?.district             ?? '',
  gender_preference:    props.job?.gender_preference    ?? 'any',
  application_deadline: props.job?.deadline_raw         ?? '',
  status:               props.job?.status               ?? 'published', // ← tracked in form
  skills:               (props.job?.skills ?? []).map(s => ({ id: s.id, required: s.is_required })),
})

// ── Skill Selection ───────────────────────────────────────────────────────────
// Use a separate ref so Vue tracks reactivity properly
const selectedSkills = ref([...form.skills])

// Keep form.skills in sync with selectedSkills
function toggleSkill(skill) {
  const idx = selectedSkills.value.findIndex(s => s.id === skill.id)
  if (idx >= 0) {
    selectedSkills.value.splice(idx, 1)
  } else {
    selectedSkills.value.push({ id: skill.id, required: true })
  }
  form.skills = [...selectedSkills.value]
}

function isSkillSelected(skill) {
  return selectedSkills.value.some(s => s.id === skill.id)
}

function toggleSkillRequired(skillId) {
  const entry = selectedSkills.value.find(s => s.id === skillId)
  if (entry) {
    entry.required = !entry.required
    form.skills = [...selectedSkills.value]
  }
}

const skillsCount = computed(() => selectedSkills.value.length)

const skillsByCategory = computed(() => {
  return (props.skills ?? []).reduce((acc, skill) => {
    const cat = skill.category ?? 'General'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(skill)
    return acc
  }, {})
})

// ── Submission — publish or draft ─────────────────────────────────────────────
function submitAs(status) {
  form.status = status          // ← set status BEFORE submitting
  form.skills = [...selectedSkills.value]

  if (isEditing.value) {
    form.patch(route('employer.jobs.update', props.job.id))
  } else {
    form.post(route('employer.jobs.store'))
  }
}

// ── Multi-step navigation ─────────────────────────────────────────────────────
const steps = ['Basic Info', 'Details', 'Requirements', 'Skills & Deadline']
const currentStep = ref(0)

function nextStep() { if (currentStep.value < steps.length - 1) currentStep.value++ }
function prevStep() { if (currentStep.value > 0) currentStep.value-- }
</script>

<template>
  <EmployerLayout :title="isEditing ? 'Edit Job Post' : 'Post a New Job'">
    <div class="max-w-3xl mx-auto">

      <!-- Step Indicator -->
      <div class="flex gap-2 mb-8">
        <button
          v-for="(step, i) in steps" :key="i"
          @click="currentStep = i"
          class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium transition-colors"
          :class="i === currentStep
            ? 'bg-emerald-500 text-white'
            : i < currentStep
              ? 'bg-emerald-100 text-emerald-700'
              : 'bg-slate-100 text-slate-400'"
        >
          <span class="w-5 h-5 rounded-full text-xs flex items-center justify-center font-bold"
            :class="i < currentStep ? 'bg-emerald-600 text-white' : ''">
            {{ i < currentStep ? '✓' : i + 1 }}
          </span>
          <span class="hidden sm:inline">{{ step }}</span>
        </button>
      </div>

      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">

        <!-- ── Step 1: Basic Info ──────────────────────────────────────── -->
        <div v-show="currentStep === 0" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800 mb-6">Basic Information</h2>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Job Title *</label>
            <input v-model="form.title" type="text" placeholder="e.g. Senior PHP Developer"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-emerald-400"
              :class="form.errors.title ? 'border-red-300' : 'border-slate-200'" />
            <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Category *</label>
            <select v-model="form.category_id"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400">
              <option value="">Select a category</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
            </select>
            <p v-if="form.errors.category_id" class="mt-1 text-xs text-red-500">{{ form.errors.category_id }}</p>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Job Type *</label>
              <select v-model="form.job_type"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400">
                <option value="full_time">Full Time</option>
                <option value="part_time">Part Time</option>
                <option value="contract">Contract</option>
                <option value="internship">Internship</option>
                <option value="freelance">Freelance</option>
                <option value="remote">Remote</option>
                <option value="hybrid">Hybrid</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Vacancies *</label>
              <input v-model.number="form.vacancies" type="number" min="1"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Job Description *</label>
            <textarea v-model="form.description" rows="8"
              placeholder="Describe the role, responsibilities, and what success looks like…"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 resize-y" />
            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
          </div>
        </div>

        <!-- ── Step 2: Location & Salary ──────────────────────────────── -->
        <div v-show="currentStep === 1" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800 mb-6">Location & Compensation</h2>

          <div class="flex items-center gap-3">
            <input type="checkbox" id="is_remote" v-model="form.is_remote" class="accent-emerald-500" />
            <label for="is_remote" class="text-sm font-medium text-slate-700">This is a remote job</label>
          </div>

          <div v-if="!form.is_remote" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
              <input v-model="form.location" type="text" placeholder="Full address / area"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">District</label>
              <input v-model="form.district" type="text" placeholder="e.g. Dhaka"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Salary Type *</label>
            <div class="flex gap-3 flex-wrap">
              <label v-for="t in ['monthly','yearly','hourly','negotiable']" :key="t"
                class="flex items-center gap-1.5 cursor-pointer text-sm">
                <input type="radio" :value="t" v-model="form.salary_type" class="accent-emerald-500" />
                {{ t.charAt(0).toUpperCase() + t.slice(1) }}
              </label>
            </div>
          </div>

          <div v-if="form.salary_type !== 'negotiable'" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Min Salary (BDT)</label>
              <input v-model.number="form.salary_min" type="number" placeholder="e.g. 30000"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Salary (BDT)</label>
              <input v-model.number="form.salary_max" type="number" placeholder="e.g. 60000"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <div class="flex items-center gap-3">
            <input type="checkbox" id="show_salary" v-model="form.show_salary" class="accent-emerald-500" />
            <label for="show_salary" class="text-sm text-slate-600">Show salary publicly on the listing</label>
          </div>
        </div>

        <!-- ── Step 3: Requirements ────────────────────────────────────── -->
        <div v-show="currentStep === 2" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800 mb-6">Candidate Requirements</h2>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Experience Level *</label>
              <select v-model="form.experience_level"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400">
                <option value="entry">Entry Level</option>
                <option value="junior">Junior (1-2 yrs)</option>
                <option value="mid">Mid Level (3-5 yrs)</option>
                <option value="senior">Senior (5+ yrs)</option>
                <option value="lead">Lead / Manager</option>
                <option value="executive">Executive</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Gender Preference</label>
              <select v-model="form.gender_preference"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400">
                <option value="any">Any</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Requirements</label>
            <textarea v-model="form.requirements" rows="6"
              placeholder="List the must-have qualifications, skills, and experience…"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 resize-y" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Benefits</label>
            <textarea v-model="form.benefits" rows="5"
              placeholder="Describe perks, benefits, and why someone would love working here…"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400 resize-y" />
          </div>
        </div>

        <!-- ── Step 4: Skills & Deadline ──────────────────────────────── -->
        <div v-show="currentStep === 3" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800 mb-6">Skills & Application Deadline</h2>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-3">
              Skills
              <!-- ✅ Fixed: uses computed skillsCount so it updates reactively -->
              <span class="text-slate-400 font-normal">({{ skillsCount }} selected)</span>
            </label>

            <div v-for="(skillGroup, category) in skillsByCategory" :key="category" class="mb-4">
              <p class="text-xs font-semibold text-slate-400 uppercase tracking-wide mb-2">{{ category }}</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="skill in skillGroup" :key="skill.id"
                  type="button"
                  @click="toggleSkill(skill)"
                  class="px-3 py-1.5 rounded-full text-xs font-medium border transition-colors"
                  :class="isSkillSelected(skill)
                    ? 'bg-indigo-500 border-indigo-500 text-white'
                    : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300'"
                >
                  {{ skill.name }}
                </button>
              </div>
            </div>

            <!-- Selected skills — required/optional toggle -->
            <div v-if="skillsCount > 0" class="mt-4 p-4 bg-slate-50 rounded-xl">
              <p class="text-xs font-semibold text-slate-500 mb-3">Mark as Required / Nice-to-have:</p>
              <div class="flex flex-wrap gap-2">
                <button
                  v-for="s in selectedSkills" :key="s.id"
                  type="button"
                  @click="toggleSkillRequired(s.id)"
                  class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                  :class="s.required
                    ? 'bg-indigo-100 border-indigo-300 text-indigo-700'
                    : 'bg-slate-100 border-slate-200 text-slate-500'"
                >
                  {{ props.skills.find(sk => sk.id === s.id)?.name }}
                  · {{ s.required ? 'Required' : 'Optional' }}
                </button>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Application Deadline *</label>
            <input v-model="form.application_deadline" type="date"
              :min="new Date(Date.now() + 86400000).toISOString().split('T')[0]"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            <p v-if="form.errors.application_deadline" class="mt-1 text-xs text-red-500">
              {{ form.errors.application_deadline }}
            </p>
          </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8 pt-6 border-t border-slate-100">
          <button v-if="currentStep > 0" type="button" @click="prevStep"
            class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
            ← Back
          </button>
          <div v-else></div>

          <div class="flex gap-3">
            <!-- Last step buttons -->
            <template v-if="currentStep === steps.length - 1">
              <!-- ✅ Fixed: calls submitAs('draft') directly -->
              <button type="button" @click="submitAs('draft')" :disabled="form.processing"
                class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 disabled:opacity-50">
                {{ form.processing ? '…' : 'Save Draft' }}
              </button>
              <!-- ✅ Fixed: calls submitAs('published') directly -->
              <button type="button" @click="submitAs('published')" :disabled="form.processing"
                class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition-colors disabled:opacity-60">
                {{ form.processing ? 'Saving…' : (isEditing ? 'Save Changes' : 'Publish Job') }}
              </button>
            </template>

            <!-- Steps 1-3: Next button -->
            <button v-else type="button" @click="nextStep"
              class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold transition-colors">
              Next →
            </button>
          </div>
        </div>

      </div>
    </div>
  </EmployerLayout>
</template>
