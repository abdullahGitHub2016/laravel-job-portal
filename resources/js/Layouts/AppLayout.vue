<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })

const page    = usePage()
const auth    = computed(() => page.props.auth)
const flash   = computed(() => page.props.flash)
const isSeeker   = computed(() => auth.value.user?.user_type === 'job_seeker')
const isEmployer = computed(() => auth.value.user?.user_type === 'employer')
</script>

<template>
  <Head :title="title ? `${title} — MyJobs` : 'MyJobs'" />
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <Link :href="route('home')" class="flex items-center gap-2 font-bold text-xl text-slate-900">
          <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center">
            <span class="text-white text-sm font-black">MJ</span>
          </div>
          My<span class="text-emerald-600">Jobs</span>
        </Link>
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <Link :href="route('jobs.index')" class="text-slate-600 hover:text-emerald-600 transition-colors">Browse Jobs</Link>
        </nav>
        <div class="flex items-center gap-3 text-sm">
          <template v-if="auth.user">
            <Link v-if="isSeeker"   :href="route('seeker.dashboard')"   class="px-4 py-2 text-slate-700 hover:bg-slate-100 rounded-lg">Dashboard</Link>
            <Link v-if="isEmployer" :href="route('employer.dashboard')" class="px-4 py-2 text-slate-700 hover:bg-slate-100 rounded-lg">Employer Dashboard</Link>
            <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 text-slate-500 hover:text-red-600">Sign Out</Link>
          </template>
          <template v-else>
            <Link :href="route('login')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Sign In</Link>
            <Link :href="route('register.seeker')" class="px-4 py-2 bg-emerald-500 hover:bg-emerald-600 text-white rounded-lg font-medium">Get Started</Link>
          </template>
        </div>
      </div>
    </header>
    <div v-if="flash?.success || flash?.error || flash?.info" class="max-w-7xl mx-auto w-full px-4 pt-4">
      <div v-if="flash.success" class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">{{ flash.success }}</div>
      <div v-if="flash.error"   class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ flash.error }}</div>
      <div v-if="flash.info"    class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">{{ flash.info }}</div>
    </div>
    <main class="flex-1"><slot /></main>
    <footer class="bg-slate-900 text-slate-400 py-10 mt-auto">
      <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <p>© {{ new Date().getFullYear() }} MyJobs — Bangladesh's Premier Job Portal</p>
      </div>
    </footer>
  </div>
</template>
