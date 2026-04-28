<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

defineProps({ jobs: Object })

function deleteJob(id) {
  if (confirm('Delete this job post?')) {
    useForm({}).delete(route('employer.jobs.destroy', id))
  }
}

function updateStatus(id, status) {
  useForm({ status }).patch(route('employer.jobs.status', id))
}
</script>

<template>
  <EmployerLayout title="My Job Posts">
    <div class="flex justify-end mb-6">
      <Link :href="route('employer.jobs.create')" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-lg">+ Post New Job</Link>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
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
                :class="job.status === 'published' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
                {{ job.status }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex items-center gap-2 justify-end">
                <Link :href="route('employer.applicants.index', job.id)" class="text-xs text-indigo-600 hover:underline">Applicants</Link>
                <Link :href="route('employer.jobs.edit', job.id)" class="text-xs text-slate-600 hover:underline">Edit</Link>
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
  </EmployerLayout>
</template>
