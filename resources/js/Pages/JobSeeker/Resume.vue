<script setup>
// Pages/JobSeeker/Resume.vue
// ─────────────────────────────────────────────────────────────────────────────
// Web-viewable resume rendered from profile data.
// This same URL is screenshotted by Browsershot for PDF export.
// Print-friendly via @media print styles.
// ─────────────────────────────────────────────────────────────────────────────

import { computed } from 'vue'
import SeekerLayout from '@/Layouts/SeekerLayout.vue'

const props = defineProps({ profile: Object })

const user = computed(() => props.profile.user)

const sortedExperiences = computed(() =>
  [...(props.profile.work_experiences ?? [])].sort((a, b) =>
    new Date(b.start_date) - new Date(a.start_date)
  )
)

const sortedEducations = computed(() =>
  [...(props.profile.educations ?? [])].sort((a, b) => b.passing_year - a.passing_year)
)

function formatDate(dateStr) {
  if (!dateStr) return 'Present'
  return new Date(dateStr).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })
}
</script>

<template>
  <!-- Wrap in SeekerLayout for the dashboard, but remove it for PDF URL -->
  <SeekerLayout title="My Resume">

    <!-- Action bar (hidden in print) -->
    <div class="flex justify-end gap-3 mb-6 print:hidden">
      <a :href="route('seeker.resume.pdf')" target="_blank"
        class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg transition-colors flex items-center gap-2">
        <span>📥</span> Download PDF
      </a>
    </div>

    <!-- ── Resume Document ────────────────────────────────────────────── -->
    <div id="resume-document"
      class="bg-white shadow-sm border border-slate-100 rounded-2xl max-w-3xl mx-auto p-12 font-serif print:shadow-none print:border-none print:rounded-none print:p-0">

      <!-- Header -->
      <header class="border-b-2 border-slate-900 pb-6 mb-8">
        <h1 class="text-4xl font-bold text-slate-900 tracking-tight">{{ user.name }}</h1>
        <p v-if="profile.headline" class="text-lg text-slate-600 mt-1">{{ profile.headline }}</p>

        <div class="flex flex-wrap gap-4 mt-4 text-sm text-slate-500">
          <span v-if="user.email">✉ {{ user.email }}</span>
          <span v-if="user.phone">📞 {{ user.phone }}</span>
          <span v-if="profile.district">📍 {{ profile.district }}, Bangladesh</span>
          <span v-if="profile.years_of_experience">{{ profile.years_of_experience }} years experience</span>
        </div>
      </header>

      <!-- Summary -->
      <section v-if="profile.bio" class="mb-8">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Summary</h2>
        <p class="text-slate-700 leading-relaxed">{{ profile.bio }}</p>
      </section>

      <!-- Work Experience -->
      <section v-if="sortedExperiences.length" class="mb-8">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Experience</h2>
        <div class="space-y-6">
          <div v-for="exp in sortedExperiences" :key="exp.id">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="font-bold text-slate-900">{{ exp.job_title }}</h3>
                <p class="text-slate-600 text-sm">{{ exp.company_name }}
                  <span v-if="exp.location"> · {{ exp.location }}</span>
                </p>
              </div>
              <span class="text-xs text-slate-400 whitespace-nowrap ml-4">
                {{ formatDate(exp.start_date) }} — {{ exp.is_current ? 'Present' : formatDate(exp.end_date) }}
              </span>
            </div>
            <p v-if="exp.responsibilities" class="text-slate-600 text-sm mt-2 leading-relaxed">
              {{ exp.responsibilities }}
            </p>
          </div>
        </div>
      </section>

      <!-- Education -->
      <section v-if="sortedEducations.length" class="mb-8">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4">Education</h2>
        <div class="space-y-4">
          <div v-for="edu in sortedEducations" :key="edu.id" class="flex items-start justify-between">
            <div>
              <h3 class="font-bold text-slate-900">{{ edu.degree }} in {{ edu.field_of_study }}</h3>
              <p class="text-sm text-slate-600">{{ edu.institution_name }}</p>
              <p v-if="edu.result_value" class="text-xs text-slate-400">{{ edu.result_value }}</p>
            </div>
            <span class="text-xs text-slate-400 ml-4">{{ edu.passing_year }}</span>
          </div>
        </div>
      </section>

      <!-- Skills -->
      <section v-if="profile.skills?.length">
        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">Skills</h2>
        <div class="flex flex-wrap gap-2">
          <span
            v-for="skill in profile.skills" :key="skill.id"
            class="px-3 py-1 border border-slate-300 text-slate-700 text-sm rounded-full"
          >
            {{ skill.name }}
            <span class="text-slate-400 text-xs ml-1">· {{ skill.pivot?.proficiency }}</span>
          </span>
        </div>
      </section>

    </div>
  </SeekerLayout>
</template>

<style>
@media print {
  body { background: white !important; }
  #resume-document { max-width: 100% !important; }
}
</style>
