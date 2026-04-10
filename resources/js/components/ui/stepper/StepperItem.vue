<script setup lang="ts">
import type { StepperItemProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { StepperItem, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<StepperItemProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <StepperItem
    v-slot="slotProps"
    data-slot="stepper-item"
    v-bind="forwardedProps"
    :class="cn('flex flex-1 flex-col items-center gap-2', props.class)"
  >
    <slot v-bind="slotProps" />
  </StepperItem>
</template>
