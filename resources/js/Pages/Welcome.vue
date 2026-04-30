<script setup>
import { Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
defineProps({ categories: Array })
</script>

<template>
  <AppLayout title="Find Your Dream Job in Bangladesh">
    <section class="bg-linear-to-br from-slate-800 to-slate-900 py-16 md:py-24 text-center">
      <div class="max-w-4xl mx-auto px-4">
        <h1 class="text-3xl md:text-5xl font-bold text-white mb-4">Find Your Dream Job</h1>
        <p class="text-slate-400 text-base md:text-lg mb-8 md:mb-10">Thousands of jobs across Bangladesh — updated daily</p>
        <div class="flex flex-col sm:flex-row gap-3 max-w-2xl mx-auto">
          <Link :href="route('jobs.index')"
            class="flex-1 text-center py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition-colors text-base md:text-lg">
            Browse All Jobs →
          </Link>
          <Link :href="route('register.employer')"
            class="px-6 py-3.5 border border-slate-600 text-slate-300 hover:bg-slate-800 rounded-xl transition-colors text-center">
            Post a Job
          </Link>
        </div>
      </div>
    </section>
    <section class="max-w-7xl mx-auto px-4 py-12 md:py-16">
      <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Browse by Category</h2>
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
        <Link v-for="cat in $page.props.categories" :key="cat.id"
          :href="route('jobs.index', { category: cat.slug })"
          class="p-4 bg-white rounded-xl border border-slate-100 shadow-sm hover:border-emerald-200 hover:shadow-md transition-all text-center group">
          <p class="font-medium text-slate-800 text-sm group-hover:text-emerald-700">{{ cat.name }}</p>
          <p class="text-xs text-slate-400 mt-1">{{ cat.job_count }} jobs</p>
        </Link>
      </div>
    </section>
  </AppLayout>
</template>