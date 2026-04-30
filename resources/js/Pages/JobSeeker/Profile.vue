<script setup>
// Pages/JobSeeker/Profile.vue
// ─────────────────────────────────────────────────────────────────────────────
// Full profile/resume builder with tabbed sections.
// All sections submit independently via Inertia — no full page reload.
// ─────────────────────────────────────────────────────────────────────────────

import { ref, reactive } from 'vue'
import { useForm } from '@inertiajs/vue3'
import SeekerLayout from '@/Layouts/SeekerLayout.vue'

const props = defineProps({
  profile:         Object,
  availableSkills: Array,
})

// ── Tab Navigation ────────────────────────────────────────────────────────────
const tabs = ['Personal', 'Education', 'Experience', 'Skills', 'Resume Upload']
const activeTab = ref(0)

// ── Personal Info Form ────────────────────────────────────────────────────────
const personalForm = useForm({
  headline:            props.profile.headline            ?? '',
  bio:                 props.profile.bio                 ?? '',
  current_job_title:   props.profile.current_job_title   ?? '',
  current_company:     props.profile.current_company     ?? '',
  years_of_experience: props.profile.years_of_experience ?? 0,
  district:            props.profile.district            ?? '',
  gender:              props.profile.gender              ?? '',
  date_of_birth:       props.profile.date_of_birth       ?? '',
  expected_salary_min: props.profile.expected_salary_min ?? null,
  expected_salary_max: props.profile.expected_salary_max ?? null,
  job_seeking_status:  props.profile.job_seeking_status  ?? 'actively_looking',
  is_profile_public:   props.profile.is_profile_public   ?? true,
})

// ── Education Form ────────────────────────────────────────────────────────────
const eduForm = useForm({
  degree:               '',
  field_of_study:       '',
  institution_name:     '',
  board_or_university:  '',
  passing_year:         new Date().getFullYear(),
  result_value:         '',
  is_highest_education: false,
})

function submitEducation() {
  eduForm.post(route('seeker.education.store'), {
    onSuccess: () => eduForm.reset(),
  })
}

function removeEducation(id) {
  useForm({}).delete(route('seeker.education.destroy', id), { preserveScroll: true })
}

// ── Work Experience Form ──────────────────────────────────────────────────────
const expForm = useForm({
  company_name:     '',
  job_title:        '',
  employment_type:  'Full-time',
  location:         '',
  start_date:       '',
  end_date:         '',
  is_current:       false,
  responsibilities: '',
})

function submitExperience() {
  expForm.post(route('seeker.experience.store'), {
    onSuccess: () => expForm.reset(),
  })
}

function removeExperience(id) {
  useForm({}).delete(route('seeker.experience.destroy', id), { preserveScroll: true })
}

// ── Skills ────────────────────────────────────────────────────────────────────
const selectedSkills = ref(
  props.profile.skills?.map(s => ({
    id:         s.id,
    name:       s.name,
    proficiency: s.pivot?.proficiency ?? 'intermediate',
    years_used:  s.pivot?.years_used  ?? 0,
  })) ?? []
)

const skillSearch = ref('')
const filteredSkills = ref(props.availableSkills)

function searchSkills() {
  filteredSkills.value = props.availableSkills.filter(s =>
    s.name.toLowerCase().includes(skillSearch.value.toLowerCase()) &&
    !selectedSkills.value.find(sel => sel.id === s.id)
  )
}

function addSkill(skill) {
  if (!selectedSkills.value.find(s => s.id === skill.id)) {
    selectedSkills.value.push({ id: skill.id, name: skill.name, proficiency: 'intermediate', years_used: 0 })
    skillSearch.value = ''
    filteredSkills.value = []
  }
}

function removeSkill(id) {
  selectedSkills.value = selectedSkills.value.filter(s => s.id !== id)
}

function saveSkills() {
  useForm({ skills: selectedSkills.value }).post(route('seeker.skills.sync'), { preserveScroll: true })
}

// ── Resume Upload ─────────────────────────────────────────────────────────────
const resumeForm = useForm({ resume_file: null })

function handleResumeUpload(e) {
  resumeForm.resume_file = e.target.files[0]
}

function submitResume() {
  resumeForm.post(route('seeker.profile.update'), {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      resumeForm.reset()
    },
  })
}
</script>

<template>
  <SeekerLayout title="My Profile">

    <!-- Profile completion banner -->
    <div class="bg-bd-pink-50 border border-bd-pink-200 rounded-xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between">
      <div class="flex items-center gap-3">
        <span class="text-bd-pink-500 text-xl">⚠️</span>
        <div>
          <p class="text-sm font-semibold text-bd-pink-800">Complete your profile to get noticed by employers</p>
          <p class="text-xs text-bd-pink-600 mt-0.5">Add education, skills, and work experience to stand out</p>
        </div>
      </div>
      <a :href="route('seeker.resume.pdf')" target="_blank"
        class="px-4 py-2 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-medium rounded-lg transition-colors">
        Download PDF
      </a>
    </div>

    <!-- Tab Navigation -->
    <div class="flex gap-1 border-b border-slate-200 mb-6 overflow-x-auto">
      <button
        v-for="(tab, i) in tabs" :key="i"
        @click="activeTab = i"
        class="px-4 py-2.5 text-sm font-medium whitespace-nowrap border-b-2 transition-colors"
        :class="activeTab === i
          ? 'border-bd-pink-500 text-bd-pink-700'
          : 'border-transparent text-slate-500 hover:text-slate-700'"
      >
        {{ tab }}
      </button>
    </div>

    <!-- ── Tab: Personal ─────────────────────────────────────────────── -->
    <div v-show="activeTab === 0">
      <form @submit.prevent="personalForm.patch(route('seeker.profile.update'))" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-5">

        <div>
          <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Professional Headline</label>
          <input v-model="personalForm.headline" type="text"
            placeholder="e.g. Senior Full-Stack Developer | PHP & Vue.js"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
        </div>

        <div>
          <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Professional Summary</label>
          <textarea v-model="personalForm.bio" rows="5"
            placeholder="Tell employers about yourself, your goals, and what makes you unique…"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400 resize-y" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Current Job Title</label>
            <input v-model="personalForm.current_job_title" type="text"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Current Company</label>
            <input v-model="personalForm.current_company" type="text"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Years of Experience</label>
            <input v-model.number="personalForm.years_of_experience" type="number" min="0" max="50"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">District</label>
            <input v-model="personalForm.district" type="text" placeholder="e.g. Dhaka"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Job Seeking Status</label>
            <select v-model="personalForm.job_seeking_status"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400">
              <option value="actively_looking">Actively Looking</option>
              <option value="open_to_offers">Open to Offers</option>
              <option value="not_looking">Not Looking</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Expected Min Salary (BDT)</label>
            <input v-model.number="personalForm.expected_salary_min" type="number"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Expected Max Salary (BDT)</label>
            <input v-model.number="personalForm.expected_salary_max" type="number"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
        </div>

        <button type="submit" :disabled="personalForm.processing"
          class="px-6 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg disabled:opacity-60">
          {{ personalForm.processing ? 'Saving…' : 'Save Personal Info' }}
        </button>
      </form>
    </div>

    <!-- ── Tab: Education ────────────────────────────────────────────── -->
    <div v-show="activeTab === 1" class="space-y-4">

      <!-- Existing entries -->
      <div v-for="edu in profile.educations" :key="edu.id"
        class="bg-white rounded-xl border-l-4 border-l-bd-pink-400 border border-slate-100 shadow-sm p-5 flex items-start justify-between gap-4">
        <div>
          <div class="flex items-center gap-2">
            <p class="font-semibold text-slate-800">{{ edu.degree }} in {{ edu.field_of_study }}</p>
            <span v-if="edu.is_highest_education" class="px-2 py-0.5 bg-bd-pink-100 text-bd-pink-700 text-xs rounded-full font-medium">Highest</span>
          </div>
          <p class="text-sm text-slate-500">{{ edu.institution_name }}</p>
          <p class="text-xs text-slate-400 mt-1">{{ edu.passing_year }} · {{ edu.result_value }}</p>
        </div>
        <button @click="removeEducation(edu.id)" class="text-red-400 hover:text-red-600 text-xs transition-colors">Remove</button>
      </div>

      <!-- Add new entry form -->
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <h3 class="font-semibold text-bd-pink-700 mb-5">Add Education</h3>
        <form @submit.prevent="submitEducation" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Degree *</label>
              <input v-model="eduForm.degree" required type="text" placeholder="e.g. Bachelor of Science"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Field of Study *</label>
              <input v-model="eduForm.field_of_study" required type="text" placeholder="e.g. Computer Science"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Institution *</label>
            <input v-model="eduForm.institution_name" required type="text" placeholder="University / College name"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
              <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Passing Year *</label>
              <input v-model.number="eduForm.passing_year" type="number" min="1970" :max="new Date().getFullYear()"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
            </div>
            <div>
              <label class="block text-sm font-medium text-bd-pink-900 mb-1.5">Result / GPA</label>
              <input v-model="eduForm.result_value" type="text" placeholder="e.g. 3.8 out of 4"
                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
            </div>
            <div class="flex items-end pb-2.5">
              <label class="flex items-center gap-2 cursor-pointer text-sm">
                <input type="checkbox" v-model="eduForm.is_highest_education" class="accent-bd-pink-500" />
                Highest Education
              </label>
            </div>
          </div>
          <button type="submit" :disabled="eduForm.processing"
            class="px-5 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg disabled:opacity-60">
            {{ eduForm.processing ? 'Adding…' : '+ Add Education' }}
          </button>
        </form>
      </div>
    </div>

    <!-- ── Tab: Skills ───────────────────────────────────────────────── -->
    <div v-show="activeTab === 3">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 space-y-5">
        <h3 class="font-semibold text-bd-pink-700">Skills</h3>

        <!-- Search + Add -->
        <div class="relative">
          <input v-model="skillSearch" @input="searchSkills" type="text"
            placeholder="Search and add skills…"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />

          <div v-if="filteredSkills.length && skillSearch"
            class="absolute top-full left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg z-10 max-h-48 overflow-y-auto">
            <button
              v-for="skill in filteredSkills.slice(0, 10)" :key="skill.id"
              type="button"
              @click="addSkill(skill)"
              class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 transition-colors">
              {{ skill.name }}
              <span class="text-xs text-slate-400 ml-1">{{ skill.category }}</span>
            </button>
          </div>
        </div>

        <!-- Selected skills -->
        <div class="space-y-3">
          <div v-for="skill in selectedSkills" :key="skill.id"
            class="flex items-center gap-3 p-3 bg-bd-pink-50 rounded-lg">
            <span class="font-medium text-slate-700 text-sm w-32 flex-shrink-0">{{ skill.name }}</span>
            <select v-model="skill.proficiency" class="flex-1 px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-bd-pink-400">
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
              <option value="expert">Expert</option>
            </select>
            <input v-model.number="skill.years_used" type="number" min="0" max="50" placeholder="Yrs"
              class="w-16 px-2 py-1.5 border border-slate-200 rounded text-sm focus:outline-none focus:border-bd-pink-400" />
            <button @click="removeSkill(skill.id)" class="text-red-400 hover:text-red-600">✕</button>
          </div>
          <p v-if="!selectedSkills.length" class="text-slate-400 text-sm text-center py-6">
            No skills added yet. Search above to add.
          </p>
        </div>

        <button @click="saveSkills" type="button"
          class="px-6 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg">
          Save Skills
        </button>
      </div>
    </div>

    <!-- ── Tab: Resume Upload ────────────────────────────────────────── -->
    <div v-show="activeTab === 4">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
        <h3 class="font-semibold text-bd-pink-700 mb-2">Upload Resume PDF</h3>
        <p class="text-sm text-slate-500 mb-6">Upload a PDF resume. This will be sent to employers when you apply.</p>

        <div v-if="profile.resume_file" class="mb-5 p-4 bg-bd-pink-50 border border-bd-pink-200 rounded-xl flex items-center gap-3">
          <span class="text-2xl">📄</span>
          <div>
            <p class="text-sm font-medium text-bd-blue-800">Resume uploaded</p>
            <a :href="route('seeker.resume.pdf')" target="_blank"
              class="text-xs text-bd-pink-600 hover:underline">View / Download →</a>
          </div>
        </div>

        <form @submit.prevent="submitResume" class="space-y-4">
          <input type="file" accept=".pdf" @change="handleResumeUpload"
            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0
                   file:text-sm file:font-semibold file:bg-bd-pink-50 file:text-bd-pink-700 hover:file:bg-bd-pink-100" />
          <button type="submit" :disabled="resumeForm.processing || !resumeForm.resume_file"
            class="px-6 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg disabled:opacity-50">
            {{ resumeForm.processing ? 'Uploading…' : 'Upload Resume' }}
          </button>
        </form>
      </div>
    </div>

  </SeekerLayout>
</template>