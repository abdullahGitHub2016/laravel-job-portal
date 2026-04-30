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
  <div class="min-h-screen bg-slate-50 flex">

    <!-- Mobile overlay -->
    <Transition name="fade">
      <div v-if="sidebarOpen" @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/40 z-20 lg:hidden" />
    </Transition>

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full z-30 transition-transform duration-300"
      :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
      <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
        <Link :href="route('home')" class="font-bold text-lg">My<span class="text-bd-blue-600">Jobs</span></Link>
        <button @click="sidebarOpen = false" class="lg:hidden p-1 text-slate-400 hover:text-slate-600">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <div class="px-6 py-4 border-b border-slate-100">
        <p class="font-semibold text-slate-800 text-sm truncate">{{ user?.name }}</p>
        <p class="text-xs text-slate-400 truncate">{{ user?.email }}</p>
      </div>
      <nav class="flex-1 px-3 py-4 space-y-1">
        <Link v-for="item in nav" :key="item.route" :href="route(item.route)"
          @click="sidebarOpen = false"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors text-slate-600 hover:bg-slate-50">
          <span>{{ item.icon }}</span>{{ item.label }}
        </Link>
      </nav>
      <div class="p-4 border-t border-slate-100">
        <Link :href="route('logout')" method="post" as="button"
          class="w-full text-left px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg">Sign Out</Link>
      </div>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col lg:ml-64">
      <header class="h-16 bg-white border-b border-slate-200 px-4 lg:px-8 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-3">
          <button @click="sidebarOpen = true"
            class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
          </button>
          <h1 class="font-semibold text-slate-800">{{ title }}</h1>
        </div>
      </header>
      <div v-if="flash?.success || flash?.error" class="px-4 lg:px-8 pt-4">
        <div v-if="flash.success" class="px-4 py-3 bg-bd-blue-50 border border-bd-blue-200 text-bd-blue-700 rounded-lg text-sm">{{ flash.success }}</div>
        <div v-if="flash.error"   class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ flash.error }}</div>
      </div>
      <main class="flex-1 p-4 lg:p-8 overflow-auto"><slot /></main>
    </div>
  </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to       { opacity: 0; }
</style>