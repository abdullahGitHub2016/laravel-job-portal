<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

defineProps({ jobs: Object })

function deleteJob(id) {
  if (confirm('Delete this job post?')) {
    useForm({}).delete(route('employer.jobs.destroy', { job: id }))
  }
}
</script>

<template>
  <EmployerLayout title="My Job Posts">
    <div class="flex justify-end mb-5">
      <Link :href="route('employer.jobs.create')"
        class="px-4 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg transition-colors">
        + Post New Job
      </Link>
    </div>

    <!-- Desktop table -->
    <div class="hidden sm:block bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="text-left px-6 py-3 font-semibold text-slate-600">Job Title</th>
              <th class="text-left px-6 py-3 font-semibold text-slate-600">Applicants</th>
              <th class="text-left px-6 py-3 font-semibold text-slate-600">Deadline</th>
              <th class="text-left px-6 py-3 font-semibold text-slate-600">Status</th>
              <th class="px-6 py-3"></th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-50">
            <tr v-for="job in jobs.data" :key="job.id" class="hover:bg-slate-50">
              <td class="px-6 py-4 font-medium text-slate-800">{{ job.title }}</td>
              <td class="px-6 py-4 text-slate-500">{{ job.application_count }}</td>
              <td class="px-6 py-4 text-slate-500">{{ job.application_deadline }}</td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                  :class="job.status === 'published' ? 'bg-bd-pink-100 text-bd-pink-700' : 'bg-slate-100 text-slate-500'">
                  {{ job.status }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3 justify-end">
                  <Link :href="route('employer.applicants.index', { job: job.id })" class="text-xs text-bd-blue-600 hover:underline">Applicants</Link>
                  <Link :href="route('employer.jobs.edit', { job: job.id })" class="text-xs text-slate-600 hover:underline">Edit</Link>
                  <button @click="deleteJob(job.id)" class="text-xs text-red-400 hover:underline">Delete</button>
                </div>
              </td>
            </tr>
            <tr v-if="!jobs.data?.length">
              <td colspan="5" class="px-6 py-12 text-center text-slate-400">No job posts yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Mobile card list -->
    <div class="sm:hidden space-y-3">
      <div v-if="!jobs.data?.length" class="text-center py-16 text-slate-400 bg-white rounded-2xl border border-slate-100">
        No job posts yet.
      </div>
      <div v-for="job in jobs.data" :key="job.id"
        class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
        <div class="flex items-start justify-between gap-2 mb-3">
          <p class="font-semibold text-slate-800 text-sm leading-snug flex-1">{{ job.title }}</p>
          <span class="px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0"
            :class="job.status === 'published' ? 'bg-bd-pink-100 text-bd-pink-700' : 'bg-slate-100 text-slate-500'">
            {{ job.status }}
          </span>
        </div>
        <div class="flex gap-4 text-xs text-slate-500 mb-3">
          <span>👥 {{ job.application_count }} applicants</span>
          <span>📅 {{ job.application_deadline }}</span>
        </div>
        <div class="flex gap-2 pt-3 border-t border-slate-100">
          <Link :href="route('employer.applicants.index', { job: job.id })"
            class="flex-1 text-center py-2 text-xs font-medium bg-bd-pink-50 text-bd-pink-700 rounded-lg hover:bg-bd-pink-100 transition-colors">
            View Applicants
          </Link>
          <Link :href="route('employer.jobs.edit', { job: job.id })"
            class="px-4 py-2 text-xs font-medium border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors">
            Edit
          </Link>
          <button @click="deleteJob(job.id)"
            class="px-3 py-2 text-xs text-red-400 hover:bg-red-50 rounded-lg transition-colors">
            🗑
          </button>
        </div>
      </div>
    </div>

  </EmployerLayout>
</template>