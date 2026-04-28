<script setup>
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({ email: '', password: '', remember: false })
const submit = () => form.post(route('login'))
</script>

<template>
  <AppLayout title="Sign In">
    <div class="min-h-[80vh] flex items-center justify-center px-4">
      <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Welcome back</h1>
        <p class="text-slate-500 text-sm mb-8">Sign in to your MyJobs account</p>

        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
            <input v-model="form.email" type="email" required autocomplete="email"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400"
              :class="form.errors.email ? 'border-red-300' : ''" />
            <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
            <input v-model="form.password" type="password" required autocomplete="current-password"
              class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-emerald-400" />
            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
          </div>
          <button type="submit" :disabled="form.processing"
            class="w-full py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-lg transition-colors disabled:opacity-60">
            {{ form.processing ? 'Signing in…' : 'Sign In' }}
          </button>
        </form>

        <div class="mt-6 text-center text-sm text-slate-500 space-y-2">
          <p>New to MyJobs?</p>
          <div class="flex gap-3 justify-center">
            <Link :href="route('register.seeker')" class="px-4 py-2 border border-emerald-200 text-emerald-700 rounded-lg hover:bg-emerald-50">
              Join as Job Seeker
            </Link>
            <Link :href="route('register.employer')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50">
              Post Jobs
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
