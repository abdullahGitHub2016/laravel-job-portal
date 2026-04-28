<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'


const props = defineProps({ userType: String })

const form = useForm({
  name:                  '',
  email:                 '',
  password:              '',
  password_confirmation: '',
  user_type:             props.userType ?? 'job_seeker',
})

const submit = () => form.post(route('register'))
</script>

<template>
  <AppLayout :title="form.user_type === 'employer' ? 'Register as Employer' : 'Register as Job Seeker'">
    <div class="min-h-[80vh] flex items-center justify-center px-4">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">
          {{ form.user_type === 'employer' ? 'Post jobs on MyJobs' : 'Find your dream job' }}
        </h1>
        <p class="text-slate-500 text-sm mb-8">Create your free account</p>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
            <input v-model="form.name" type="text" required
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input v-model="form.email" type="email" required
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input v-model="form.password" type="password" required
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
            <input v-model="form.password_confirmation" type="password" required
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
          </div>
          <input type="hidden" v-model="form.user_type" />
          <button type="submit" :disabled="form.processing"
            class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition-colors disabled:opacity-60">
            {{ form.processing ? 'Creating account…' : 'Create Account' }}
          </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
          Already have an account?
          <Link :href="route('login')" class="text-emerald-600 hover:underline">Sign in</Link>
        </p>
      </div>
    </div>
  </AppLayout>
</template>
