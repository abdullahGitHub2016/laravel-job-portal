<script setup>
import { useForm } from '@inertiajs/vue3'
import EmployerLayout from '@/Layouts/EmployerLayout.vue'

const props = defineProps({ profile: Object, industries: Array })

const form = useForm({
  company_name:     props.profile?.company_name     ?? '',
  company_overview: props.profile?.company_overview ?? '',
  company_type:     props.profile?.company_type     ?? '',
  company_size:     props.profile?.company_size     ?? '',
  founded_year:     props.profile?.founded_year     ?? '',
  website:          props.profile?.website          ?? '',
  address:          props.profile?.address          ?? '',
  district:         props.profile?.district         ?? '',
  industry_id:      props.profile?.industry_id      ?? '',
})
</script>

<template>
  <EmployerLayout title="Company Profile">
    <div class="max-w-2xl w-full">
      <form @submit.prevent="form.patch(route('employer.company.update'))" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 sm:p-8 space-y-5">
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Company Name *</label>
          <input v-model="form.company_name" type="text" required
            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          <p v-if="form.errors.company_name" class="mt-1 text-xs text-red-500">{{ form.errors.company_name }}</p>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Industry</label>
          <select v-model="form.industry_id" class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400">
            <option value="">Select industry</option>
            <option v-for="ind in industries" :key="ind.id" :value="ind.id">{{ ind.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-700 mb-1.5">Company Overview</label>
          <textarea v-model="form.company_overview" rows="5"
            class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400 resize-y" />
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">District</label>
            <input v-model="form.district" type="text" placeholder="e.g. Dhaka"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Website</label>
            <input v-model="form.website" type="url" placeholder="https://"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-bd-pink-400" />
          </div>
        </div>
        <button type="submit" :disabled="form.processing"
          class="px-6 py-2.5 bg-bd-pink-500 hover:bg-bd-pink-600 text-white text-sm font-semibold rounded-lg disabled:opacity-60">
          {{ form.processing ? 'Saving…' : 'Save Company Profile' }}
        </button>
      </form>
    </div>
  </EmployerLayout>
</template>