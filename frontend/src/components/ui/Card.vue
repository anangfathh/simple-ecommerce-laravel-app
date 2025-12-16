<script setup lang="ts">
import { computed } from 'vue'
import { cn } from '@/lib/utils'

interface Props {
  class?: string
}

const props = defineProps<Props>()

const cardClasses = computed(() =>
  cn(
    'rounded-xl border bg-card text-card-foreground shadow-sm transition-all duration-300 hover:shadow-md',
    props.class
  )
)
</script>

<template>
  <div :class="cardClasses">
    <slot />
  </div>
</template>
