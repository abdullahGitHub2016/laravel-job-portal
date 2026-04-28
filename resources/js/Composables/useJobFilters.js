import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { debounce } from 'lodash'

export function useJobFilters(initialFilters = {}) {
  const filters = ref({
    q:          initialFilters.q          ?? '',
    category:   initialFilters.category   ?? null,
    location:   initialFilters.location   ?? '',
    job_type:   initialFilters.job_type   ?? null,
    salary_min: initialFilters.salary_min ?? null,
    salary_max: initialFilters.salary_max ?? null,
    experience: initialFilters.experience ?? null,
    sort:       initialFilters.sort       ?? 'latest',
  })

  const hasActiveFilters = computed(() =>
    Object.entries(filters.value).some(([k, v]) => k !== 'sort' && v !== null && v !== '' && v !== undefined)
  )

  const applyFilters = debounce(() => {
    const params = Object.fromEntries(
      Object.entries(filters.value).filter(([, v]) => v !== null && v !== '' && v !== undefined)
    )
    router.get(route('jobs.index'), params, { preserveState: true, preserveScroll: true, replace: true })
  }, 400)

  function clearFilters() {
    filters.value = { sort: filters.value.sort }
    applyFilters()
  }

  watch(filters, () => applyFilters(), { deep: true })

  return { filters, hasActiveFilters, clearFilters }
}
