<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })

const page    = usePage()
const auth    = computed(() => page.props.auth)
const flash   = computed(() => page.props.flash)
const isSeeker   = computed(() => auth.value.user?.user_type === 'job_seeker')
const isEmployer = computed(() => auth.value.user?.user_type === 'employer')

const mobileMenuOpen = ref(false)
</script>

<template>
  <Head :title="title ? `${title} — MyJobs` : 'MyJobs'" />
  <div class="min-h-screen bg-slate-50 flex flex-col">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
      <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
        <Link :href="route('home')" class="flex items-center gap-2 font-bold text-xl text-slate-900">
          <div class="w-8 h-8 bg-bd-blue-500 rounded-lg flex items-center justify-center">
            <span class="text-white text-sm font-black">MJ</span>
          </div>
          My<span class="text-bd-blue-600">Jobs</span>
        </Link>
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <Link :href="route('jobs.index')" class="text-slate-600 hover:text-bd-blue-600 transition-colors">Browse Jobs</Link>
        </nav>
        <div class="hidden md:flex items-center gap-3 text-sm">
          <template v-if="auth.user">
            <Link v-if="isSeeker"   :href="route('seeker.dashboard')"   class="px-4 py-2 text-slate-700 hover:bg-slate-100 rounded-lg">Dashboard</Link>
            <Link v-if="isEmployer" :href="route('employer.dashboard')" class="px-4 py-2 text-slate-700 hover:bg-slate-100 rounded-lg">Employer Dashboard</Link>
            <Link :href="route('logout')" method="post" as="button" class="px-4 py-2 text-slate-500 hover:text-red-600">Sign Out</Link>
          </template>
          <template v-else>
            <Link :href="route('login')" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Sign In</Link>
            <Link :href="route('register.seeker')" class="px-4 py-2 bg-bd-blue-500 hover:bg-bd-blue-600 text-white rounded-lg font-medium">Get Started</Link>
          </template>
        </div>
        <button @click="mobileMenuOpen = !mobileMenuOpen"
          class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <Transition name="slide-down">
        <div v-if="mobileMenuOpen" class="md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
          <Link :href="route('jobs.index')" @click="mobileMenuOpen = false"
            class="block px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg">Browse Jobs</Link>
          <template v-if="auth.user">
            <Link v-if="isSeeker" :href="route('seeker.dashboard')" @click="mobileMenuOpen = false"
              class="block px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg">Dashboard</Link>
            <Link v-if="isEmployer" :href="route('employer.dashboard')" @click="mobileMenuOpen = false"
              class="block px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg">Employer Dashboard</Link>
            <Link :href="route('logout')" method="post" as="button" @click="mobileMenuOpen = false"
              class="block w-full text-left px-3 py-2.5 text-sm text-red-500 hover:bg-red-50 rounded-lg">Sign Out</Link>
          </template>
          <template v-else>
            <Link :href="route('login')" @click="mobileMenuOpen = false"
              class="block px-3 py-2.5 text-sm text-slate-700 hover:bg-slate-50 rounded-lg">Sign In</Link>
            <Link :href="route('register.seeker')" @click="mobileMenuOpen = false"
              class="block px-3 py-2.5 text-sm font-semibold text-bd-blue-700 bg-bd-blue-50 hover:bg-bd-blue-100 rounded-lg">Get Started</Link>
          </template>
        </div>
      </Transition>
    </header>
    <div v-if="flash?.success || flash?.error || flash?.info" class="max-w-7xl mx-auto w-full px-4 pt-4">
      <div v-if="flash.success" class="px-4 py-3 bg-bd-blue-50 border border-bd-blue-200 text-bd-blue-700 rounded-lg text-sm">{{ flash.success }}</div>
      <div v-if="flash.error"   class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ flash.error }}</div>
      <div v-if="flash.info"    class="px-4 py-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">{{ flash.info }}</div>
    </div>
    <main class="flex-1"><slot /></main>
    <footer class="bg-bd-blue-900 text-bd-blue-200 py-10 mt-auto">
      <div class="max-w-7xl mx-auto px-4 text-center text-sm">
        <p>© {{ new Date().getFullYear() }} MyJobs — Bangladesh's Premier Job Portal</p>
      </div>
    </footer>
  </div>
</template>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to       { opacity: 0; transform: translateY(-8px); }
</style>