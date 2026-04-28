<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'

defineProps({ title: String })
const page = usePage()
const user = computed(() => page.props.auth.user)
const flash = computed(() => page.props.flash)

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
    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col fixed h-full">
      <div class="h-16 flex items-center px-6 border-b border-slate-100">
        <Link :href="route('home')" class="font-bold text-lg">My<span class="text-emerald-600">Jobs</span></Link>
      </div>
      <div class="px-6 py-4 border-b border-slate-100">
        <p class="font-semibold text-slate-800 text-sm truncate">{{ user?.name }}</p>
        <p class="text-xs text-slate-400 truncate">{{ user?.email }}</p>
      </div>
      <nav class="flex-1 px-3 py-4 space-y-1">
        <Link v-for="item in nav" :key="item.route" :href="route(item.route)"
          class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors text-slate-600 hover:bg-slate-50">
          <span>{{ item.icon }}</span>{{ item.label }}
        </Link>
      </nav>
      <div class="p-4 border-t border-slate-100">
        <Link :href="route('logout')" method="post" as="button"
          class="w-full text-left px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-lg">Sign Out</Link>
      </div>
    </aside>
    <div class="flex-1 flex flex-col ml-64">
      <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between sticky top-0 z-30">
        <h1 class="font-semibold text-slate-800">{{ title }}</h1>
      </header>
      <div v-if="flash?.success || flash?.error" class="px-8 pt-4">
        <div v-if="flash.success" class="px-4 py-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-lg text-sm">{{ flash.success }}</div>
        <div v-if="flash.error"   class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ flash.error }}</div>
      </div>
      <main class="flex-1 p-8 overflow-auto"><slot /></main>
    </div>
  </div>
</template>
