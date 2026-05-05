<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'
import RichEditor from '@/Components/Shared/RichEditor.vue'

const props = defineProps({
  categories:        Array,
  skills:            Array,
  availableBenefits: Array,   // ← from controller formData()
  job:               { type: Object, default: null },
})

const isEditing = computed(() => !!props.job)

// ── Form ──────────────────────────────────────────────────────────────────────
const form = useForm({
  title:                props.job?.title                ?? '',
  description:          props.job?.description          ?? '',
  requirements:         props.job?.requirements         ?? '',
  category_id:          props.job?.category_id          ?? props.job?.category?.id ?? '',
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
  status:               props.job?.status               ?? 'published',
  skills:               [],
  benefit_ids:          [],  // synced from selectedBenefits at submit
})

// ── Skills ────────────────────────────────────────────────────────────────────
const selectedSkills = ref([])

watch(() => props.job?.skills, (skills) => {
  selectedSkills.value = (skills ?? []).map(s => ({ id: String(s.id), required: s.is_required }))
}, { immediate: true })

function toggleSkill(skill) {
  const idx = selectedSkills.value.findIndex(s => s.id === skill.id)
  if (idx >= 0) selectedSkills.value.splice(idx, 1)
  else selectedSkills.value.push({ id: skill.id, required: true })
}

function isSkillSelected(skill) {
  return selectedSkills.value.some(s => s.id === skill.id)
}

function toggleSkillRequired(skillId) {
  const entry = selectedSkills.value.find(s => s.id === skillId)
  if (entry) entry.required = !entry.required
}

const skillsCount = computed(() => selectedSkills.value.length)

const skillsByCategory = computed(() =>
  (props.skills ?? []).reduce((acc, skill) => {
    const cat = skill.category ?? 'General'
    if (!acc[cat]) acc[cat] = []
    acc[cat].push(skill)
    return acc
  }, {})
)

// ── Benefits — tracked in a plain ref so Vue reactivity works correctly ─────────
// Inertia's useForm proxy doesn't track splice/push on nested arrays,
// so we keep a separate ref and sync it into form.benefit_ids at submit time.
const selectedBenefits = ref([])

watch(() => props.job?.benefits, (val) => {
  console.log('[benefits watch]', val)   // TEMP DEBUG — remove after confirming
  selectedBenefits.value = (val ?? []).map(b => String(b.id))
}, { immediate: true, deep: true })

const benefitsByCategory = computed(() =>
  (props.availableBenefits ?? []).reduce((acc, b) => {
    if (!acc[b.category]) acc[b.category] = []
    acc[b.category].push(b)
    return acc
  }, {})
)

function toggleBenefit(id) {
  const sid = String(id)
  const idx = selectedBenefits.value.indexOf(sid)
  if (idx >= 0) selectedBenefits.value.splice(idx, 1)
  else selectedBenefits.value.push(sid)
}

function isBenefitSelected(id) {
  return selectedBenefits.value.includes(String(id))
}

const benefitsCount = computed(() => selectedBenefits.value.length)

// ── Errors ────────────────────────────────────────────────────────────────────
const allErrors = computed(() => Object.entries(form.errors))
const hasErrors = computed(() => allErrors.value.length > 0)

// ── Submit ────────────────────────────────────────────────────────────────────
function submitAs(status) {
  form.skills       = selectedSkills.value.map(s => ({ id: s.id, required: s.required }))
  form.benefit_ids  = [...selectedBenefits.value]   // ✅ sync ref → form at submit time
  form.status       = status
  form.currency     = form.currency || 'BDT'

  if (isEditing.value) {
    form.patch(route('employer.jobs.update', props.job.id))
  } else {
    form.post(route('employer.jobs.store'))
  }
}

// ── Steps ─────────────────────────────────────────────────────────────────────
const steps = ['Basic Info', 'Location & Pay', 'Requirements', 'Skills & Benefits']
const currentStep = ref(0)
const nextStep = () => { if (currentStep.value < steps.length - 1) currentStep.value++ }
const prevStep = () => { if (currentStep.value > 0) currentStep.value-- }
</script>

<template>
  <EmployerLayout :title="isEditing ? 'Edit Job Post' : 'Post a New Job'">
    <div class="max-w-3xl mx-auto">

      <!-- Step tabs -->
      <div class="flex gap-1 mb-6">
        <button v-for="(step, i) in steps" :key="i" @click="currentStep = i"
          class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-all"
          :class="i === currentStep ? 'bg-emerald-500 text-white shadow-sm'
            : i < currentStep ? 'bg-emerald-100 text-emerald-700'
            : 'bg-slate-100 text-slate-400'">
          <span class="w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">
            {{ i < currentStep ? '✓' : i + 1 }}
          </span>
          <span class="hidden sm:inline">{{ step }}</span>
        </button>
      </div>

      <!-- Global errors -->
      <div v-if="hasErrors" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl">
        <p class="text-sm font-semibold text-red-700 mb-2">⚠ Please fix these errors:</p>
        <ul class="space-y-1">
          <li v-for="[field, msg] in allErrors" :key="field" class="text-xs text-red-600">
            <span class="font-medium capitalize">{{ field.replace(/_/g, ' ') }}:</span> {{ msg }}
          </li>
        </ul>
      </div>

      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">

        <!-- ── Step 1: Basic Info ────────────────────────────────────── -->
        <div v-show="currentStep === 0" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800">Basic Information</h2>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Job Title *</label>
            <input v-model="form.title" type="text" placeholder="e.g. Senior PHP Developer"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-emerald-400"
              :class="form.errors.title ? 'border-red-300 bg-red-50' : 'border-slate-200'" />
            <p v-if="form.errors.title" class="mt-1 text-xs text-red-500">{{ form.errors.title }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Category *</label>
            <select v-model="form.category_id"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-emerald-400"
              :class="form.errors.category_id ? 'border-red-300 bg-red-50' : 'border-slate-200'">
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
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
              Job Description *
              <span class="text-slate-400 font-normal ml-1">(min 50 chars — {{ form.description.length }} typed)</span>
            </label>
            <textarea v-model="form.description" rows="8"
              placeholder="Describe the role, responsibilities, and what a typical day looks like…"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-emerald-400 resize-y"
              :class="form.errors.description ? 'border-red-300 bg-red-50' : 'border-slate-200'" />
            <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
          </div>
        </div>

        <!-- ── Step 2: Location & Salary ────────────────────────────── -->
        <div v-show="currentStep === 1" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800">Location & Compensation</h2>

          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.is_remote" class="accent-emerald-500 w-4 h-4" />
            <span class="text-sm font-medium text-slate-700">This is a remote job</span>
          </label>

          <div v-if="!form.is_remote" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Location / Area</label>
              <input v-model="form.location" type="text" placeholder="e.g. Gulshan-2, Dhaka"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">District</label>
              <input v-model="form.district" type="text" placeholder="e.g. Dhaka"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Salary Type *</label>
            <div class="flex gap-5 flex-wrap">
              <label v-for="t in ['monthly','yearly','hourly','negotiable']" :key="t"
                class="flex items-center gap-2 cursor-pointer text-sm text-slate-600">
                <input type="radio" :value="t" v-model="form.salary_type" class="accent-emerald-500" />
                {{ t.charAt(0).toUpperCase() + t.slice(1) }}
              </label>
            </div>
          </div>

          <div v-if="form.salary_type !== 'negotiable'" class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Min Salary (BDT)</label>
              <input v-model.number="form.salary_min" type="number" min="0" placeholder="e.g. 30000"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-700 mb-1.5">Max Salary (BDT)</label>
              <input v-model.number="form.salary_max" type="number" min="0" placeholder="e.g. 60000"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            </div>
          </div>

          <label class="flex items-center gap-3 cursor-pointer">
            <input type="checkbox" v-model="form.show_salary" class="accent-emerald-500 w-4 h-4" />
            <span class="text-sm text-slate-600">Show salary range publicly on listing</span>
          </label>
        </div>

        <!-- ── Step 3: Requirements (Rich Text) ──────────────────────── -->
        <div v-show="currentStep === 2" class="space-y-5">
          <h2 class="text-lg font-bold text-slate-800">Candidate Requirements</h2>

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

          <!-- ✅ Rich text editor for requirements -->
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
              Requirements
              <span class="ml-1 text-xs font-normal text-slate-400">— supports bold, bullet lists, headings</span>
            </label>
            <RichEditor
              v-model="form.requirements"
              placeholder="• Bachelor's degree in a relevant field&#10;• Minimum 3 years of experience…"
              min-height="220px"
            />
          </div>
        </div>

        <!-- ── Step 4: Skills & Benefits ────────────────────────────── -->
        <div v-show="currentStep === 3" class="space-y-6">
          <h2 class="text-lg font-bold text-slate-800">Skills & Benefits</h2>

          <!-- Skills -->
          <div>
            <p class="text-sm font-medium text-slate-700 mb-3">
              Required Skills
              <span class="ml-1 px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-full text-xs font-semibold">
                {{ skillsCount }} selected
              </span>
            </p>
            <div v-for="(group, cat) in skillsByCategory" :key="cat" class="mb-4">
              <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ cat }}</p>
              <div class="flex flex-wrap gap-2">
                <button v-for="skill in group" :key="skill.id"
                  type="button" @click="toggleSkill(skill)"
                  class="px-3 py-1.5 rounded-full text-xs font-medium border transition-all"
                  :class="isSkillSelected(skill)
                    ? 'bg-indigo-500 border-indigo-500 text-white'
                    : 'bg-white border-slate-200 text-slate-600 hover:border-indigo-300'">
                  {{ skill.name }}
                </button>
              </div>
            </div>
            <div v-if="skillsCount > 0" class="p-3 bg-slate-50 rounded-xl border border-slate-100">
              <p class="text-xs font-semibold text-slate-400 mb-2">Toggle Required / Optional:</p>
              <div class="flex flex-wrap gap-2">
                <button v-for="s in selectedSkills" :key="s.id"
                  type="button" @click="toggleSkillRequired(s.id)"
                  class="px-2.5 py-1 rounded-full text-xs font-medium border transition-colors"
                  :class="s.required ? 'bg-indigo-100 border-indigo-300 text-indigo-700' : 'bg-slate-100 border-slate-200 text-slate-500'">
                  {{ props.skills?.find(sk => sk.id === s.id)?.name }} · {{ s.required ? 'Required' : 'Optional' }}
                </button>
              </div>
            </div>
          </div>

          <!-- ✅ Benefits — grouped checkboxes from DB -->
          <div class="border-t border-slate-100 pt-5">
            <p class="text-sm font-medium text-slate-700 mb-3">
              Benefits & Perks
              <span class="ml-1 px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-semibold">
                {{ benefitsCount }} selected
              </span>
            </p>

            <!-- Empty state if benefits not seeded yet -->
            <div v-if="!availableBenefits?.length"
              class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl text-slate-400 text-sm">
              No benefits found. Run: <code class="bg-slate-100 px-1 rounded text-xs">php artisan db:seed --class=BenefitSeeder</code>
            </div>

            <div v-else>
              <div v-for="(group, cat) in benefitsByCategory" :key="cat" class="mb-5">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">{{ cat }}</p>
                <div class="grid grid-cols-2 gap-2">
                  <label v-for="benefit in group" :key="benefit.id"
                    class="flex items-center gap-2.5 p-2.5 rounded-lg border cursor-pointer transition-all select-none"
                    :class="isBenefitSelected(benefit.id)
                      ? 'border-emerald-300 bg-emerald-50'
                      : 'border-slate-200 bg-white hover:border-slate-300'">
                    <input type="checkbox"
                      :checked="isBenefitSelected(benefit.id)"
                      @change="toggleBenefit(benefit.id)"
                      class="accent-emerald-500 w-4 h-4 flex-shrink-0" />
                    <span class="text-base leading-none">{{ benefit.icon }}</span>
                    <span class="text-xs font-medium text-slate-700 leading-tight">{{ benefit.name }}</span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          <!-- Deadline -->
          <div class="border-t border-slate-100 pt-5">
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Application Deadline *</label>
            <input v-model="form.application_deadline" type="date"
              :min="new Date(Date.now() + 86400000).toISOString().split('T')[0]"
              class="w-full px-3 py-2.5 border rounded-lg text-sm focus:outline-none focus:border-emerald-400"
              :class="form.errors.application_deadline ? 'border-red-300 bg-red-50' : 'border-slate-200'" />
            <p v-if="form.errors.application_deadline" class="mt-1 text-xs text-red-500">{{ form.errors.application_deadline }}</p>
          </div>
        </div>

        <!-- Nav buttons -->
        <div class="flex justify-between mt-8 pt-6 border-t border-slate-100">
          <button v-if="currentStep > 0" type="button" @click="prevStep"
            class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50">
            ← Back
          </button>
          <div v-else></div>

          <div class="flex gap-3">
            <template v-if="currentStep === steps.length - 1">
              <button type="button" @click="submitAs('draft')" :disabled="form.processing"
                class="px-5 py-2.5 border border-slate-200 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 disabled:opacity-50">
                {{ form.processing ? '…' : '💾 Save Draft' }}
              </button>
              <button type="button" @click="submitAs('published')" :disabled="form.processing"
                class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold disabled:opacity-60 transition-colors">
                {{ form.processing ? 'Saving…' : (isEditing ? '✓ Save Changes' : '🚀 Publish Job') }}
              </button>
            </template>
            <button v-else type="button" @click="nextStep"
              class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg text-sm font-semibold">
              Next →
            </button>
          </div>
        </div>

      </div>
    </div>
  </EmployerLayout>
</template>