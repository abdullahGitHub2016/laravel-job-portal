<script setup>
import { ref, watch, nextTick } from 'vue'

const props = defineProps({
  modelValue:  { type: String, default: '' },
  placeholder: { type: String, default: 'Start typing…' },
  minHeight:   { type: String, default: '180px' },
})

const emit   = defineEmits(['update:modelValue'])
const editor = ref(null)
const focused = ref(false)

// Load content when prop changes (handles edit page)
watch(() => props.modelValue, async (val) => {
  await nextTick()
  if (editor.value && !focused.value) {
    const html = val ?? ''
    if (editor.value.innerHTML !== html) {
      editor.value.innerHTML = html
    }
  }
}, { immediate: true })

function onInput() {
  emit('update:modelValue', editor.value.innerHTML)
}

function exec(cmd, val = null) {
  editor.value.focus()
  document.execCommand(cmd, false, val)
  onInput()
}

const isEmpty = () => {
  const v = props.modelValue ?? ''
  return v === '' || v === '<br>' || v === '<div><br></div>'
}
</script>

<template>
  <div class="border rounded-lg overflow-hidden transition-all"
    :class="focused ? 'border-emerald-400 ring-1 ring-emerald-200' : 'border-slate-200'">

    <!-- Toolbar -->
    <div class="flex flex-wrap items-center gap-1 px-3 py-2 bg-slate-50 border-b border-slate-200">
      <button type="button" @mousedown.prevent="exec('bold')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 font-bold text-slate-600 transition-all">B</button>
      <button type="button" @mousedown.prevent="exec('italic')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 italic text-slate-600 transition-all">I</button>
      <button type="button" @mousedown.prevent="exec('underline')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 underline text-slate-600 transition-all">U</button>
      <div class="w-px h-4 bg-slate-300 mx-0.5"></div>
      <button type="button" @mousedown.prevent="exec('insertUnorderedList')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 text-slate-600 transition-all">• List</button>
      <button type="button" @mousedown.prevent="exec('insertOrderedList')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 text-slate-600 transition-all">1. List</button>
      <button type="button" @mousedown.prevent="exec('formatBlock', 'H3')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white border border-transparent hover:border-slate-200 font-bold text-slate-600 transition-all">H3</button>
      <div class="w-px h-4 bg-slate-300 mx-0.5"></div>
      <button type="button" @mousedown.prevent="exec('removeFormat')"
        class="px-2.5 py-1 text-xs rounded hover:bg-white text-slate-400 transition-all ml-auto">✕ Clear</button>
    </div>

    <!-- Editable -->
    <div class="relative">
      <p v-if="isEmpty() && !focused"
        class="absolute top-3 left-3 text-slate-400 text-sm pointer-events-none select-none z-10">
        {{ placeholder }}
      </p>
      <div ref="editor" contenteditable="true"
        :style="{ minHeight }"
        class="px-3 py-3 text-sm text-slate-700 focus:outline-none
               [&_ul]:list-disc [&_ul]:pl-5 [&_ul]:space-y-1
               [&_ol]:list-decimal [&_ol]:pl-5 [&_ol]:space-y-1
               [&_h3]:font-bold [&_h3]:text-slate-800 [&_h3]:text-base [&_h3]:mt-2 [&_h3]:mb-1
               [&_strong]:font-semibold [&_li]:leading-relaxed"
        @input="onInput"
        @focus="focused = true"
        @blur="focused = false" />
    </div>
  </div>
</template>