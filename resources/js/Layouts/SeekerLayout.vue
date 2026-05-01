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
  { label: 'Dashboard',    route: 'seeker.dashboard',          icon: '🏠' },
  { label: 'My Profile',   route: 'seeker.profile.edit',       icon: '👤' },
  { label: 'Applications', route: 'seeker.applications.index', icon: '📋' },
  { label: 'My Resume',    route: 'seeker.resume.show',        icon: '📄' },
]
</script>

<template>
  <Head :title="title ? `${title} — MyJobs` : 'MyJobs'" />
  <div class="min-h-screen bg-slate-50 flex flex-col">

    <!-- Top header (always visible) -->
    <header class="h-14 bg-white border-b border-slate-200 flex items-center px-4 sticky top-0 z-40">
      <button @click="sidebarOpen = !sidebarOpen"
        class="flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 transition-colors mr-3 flex-shrink-0">
        <svg width="20" height="20" fill="none" viewBox="0 0 24 24">
          <path stroke="#334155" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>
      <Link :href="route('home')" class="font-bold text-base">
        My<span class="text-bd-pink-500">Jobs</span>
      </Link>
      <span class="ml-3 text-slate-400 text-sm hidden sm:inline">|</span>
      <span class="ml-3 text-slate-600 text-sm hidden sm:inline truncate max-w-xs">{{ title }}</span>
      <div class="ml-auto flex items-center gap-2 text-sm text-slate-500">
        <span class="hidden sm:inline truncate max-w-[120px]">{{ user?.name }}</span>
      </div>
    </header>

    <!-- Body: sidebar drawer + content -->
    <div class="flex flex-1 overflow-hidden">

      <!-- Overlay -->
      <Transition name="fade">
        <div v-if="sidebarOpen" @click="sidebarOpen = false"
          class="fixed inset-0 bg-black/40 z-30 top-14" />
      </Transition>

      <!-- Sidebar: slides over content on ALL sizes, never pushes layout -->
      <aside
        class="fixed top-14 left-0 bottom-0 w-60 bg-white border-r border-slate-200 z-40 flex flex-col
               transition-transform duration-300 ease-in-out shadow-xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="px-4 py-4 border-b border-slate-100">
          <p class="font-semibold text-slate-800 text-sm truncate">{{ user?.name }}</p>
          <p class="text-xs text-slate-400 truncate mt-0.5">{{ user?.email }}</p>
        </div>

        <nav class="flex-1 px-2 py-3 space-y-0.5 overflow-y-auto">
          <Link v-for="item in nav" :key="item.route"
            :href="route(item.route)"
            @click="sidebarOpen = false"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-slate-600
                   hover:bg-slate-50 hover:text-slate-900 transition-colors">
            <span class="text-base leading-none">{{ item.icon }}</span>
            {{ item.label }}
          </Link>
        </nav>

        <div class="p-3 border-t border-slate-100">
          <Link :href="route('logout')" method="post" as="button"
            class="w-full flex items-center gap-3 px-3 py-2.5 text-sm text-red-500
                   hover:bg-red-50 rounded-lg transition-colors">
            <span class="text-base leading-none">🚪</span>
            Sign Out
          </Link>
        </div>
      </aside>

      <!-- Main content: full width, never offset -->
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