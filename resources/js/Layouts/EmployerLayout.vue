<script setup>
import { ref, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })
const page = usePage()
const user = computed(() => page.props.auth.user)
const flash = computed(() => page.props.flash)
const sidebarOpen = ref(false)

const nav = [
  { label: 'Dashboard', route: 'employer.dashboard',    icon: '📊' },
  { label: 'Job Posts', route: 'employer.jobs.index',   icon: '💼' },
  { label: 'Company',   route: 'employer.company.edit', icon: '🏢' },
]
</script>

<template>
  <Head :title="title ? `${title} — MyJobs Employer` : 'MyJobs Employer'" />
  <div class="min-h-screen bg-slate-50 flex flex-col">

    <!-- Top header -->
    <header class="h-14 bg-bd-blue-900 flex items-center px-4 sticky top-0 z-40">
      <button @click="sidebarOpen = !sidebarOpen"
        class="flex items-center justify-center w-9 h-9 rounded-lg bg-bd-blue-800 hover:bg-bd-blue-700 transition-colors mr-3 flex-shrink-0">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
          <path stroke="#ffffff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <Link :href="route('home')" class="font-bold text-base text-white">
        My<span class="text-bd-pink-400">Jobs</span>
        <span class="ml-2 text-xs text-bd-blue-400 font-normal hidden sm:inline">Employer</span>
      </Link>
      <span class="ml-3 text-bd-blue-600 hidden sm:inline">|</span>
      <span class="ml-3 text-bd-blue-300 text-sm hidden sm:inline truncate max-w-xs">{{ title }}</span>
      <div class="ml-auto text-sm text-bd-blue-300 truncate max-w-[140px] hidden sm:block">
        {{ user?.name }}
      </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

      <!-- Overlay -->
      <Transition name="fade">
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
          class="fixed inset-0 bg-black/50 z-30 top-14" />
      </Transition>

      <!-- Sidebar -->
      <aside
        class="fixed top-14 left-0 bottom-0 w-60 bg-bd-blue-900 z-40 flex flex-col
               transition-transform duration-300 ease-in-out shadow-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="px-4 py-4 border-b border-bd-blue-800">
          <p class="font-semibold text-white text-sm truncate">{{ user?.name }}</p>
          <p class="text-xs text-bd-blue-400 truncate mt-0.5">{{ user?.email }}</p>
        </div>

        <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
          <Link v-for="item in nav" :key="item.route"
            :href="route(item.route)"
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-bd-blue-200
                   hover:bg-bd-blue-800 hover:text-white transition-colors">
            <span class="text-base leading-none">{{ item.icon }}</span>
            {{ item.label }}
          </Link>
        </nav>

        <div class="p-3 border-t border-bd-blue-800">
          <Link :href="route('logout')" method="post" as="button"
            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-400
                   hover:bg-bd-blue-800 rounded-lg transition-colors">
            <span class="text-base leading-none">🚪</span>
            Sign Out
          </Link>
        </div>
      </aside>

      <!-- Main content -->
      <main class="flex-1 overflow-y-auto">
        <div v-if="flash?.success || flash?.error" class="px-4 sm:px-6 pt-4">
          <div v-if="flash.success"
            class="px-4 py-3 bg-bd-pink-50 border border-bd-pink-200 text-bd-pink-700 rounded-lg text-sm">
            {{ flash.success }}
          </div>
          <div v-if="flash.error"
            class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            {{ flash.error }}
          </div>
        </div>
        <div class="p-4 sm:p-6">
          <slot />
        </div>
      </main>

    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>