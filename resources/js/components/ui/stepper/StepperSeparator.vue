<script setup lang="ts">
import type { StepperSeparatorProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { StepperSeparator, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<StepperSeparatorProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <StepperSeparator
    data-slot="stepper-separator"
    v-bind="forwardedProps"
    :class="cn('mt-4 h-px w-full bg-border data-[state=completed]:bg-primary', props.class)"
  />
</template>
