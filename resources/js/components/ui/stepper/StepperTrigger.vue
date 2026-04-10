<script setup lang="ts">
import type { StepperTriggerProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { StepperTrigger, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<StepperTriggerProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <StepperTrigger
    data-slot="stepper-trigger"
    v-bind="forwardedProps"
    :class="cn('inline-flex flex-col items-center gap-2', props.class)"
  >
    <slot />
  </StepperTrigger>
</template>
