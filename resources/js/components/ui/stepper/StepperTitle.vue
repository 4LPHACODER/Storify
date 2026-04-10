<script setup lang="ts">
import type { StepperTitleProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { StepperTitle, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = defineProps<StepperTitleProps & { class?: HTMLAttributes["class"] }>()

const delegatedProps = reactiveOmit(props, "class")
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <StepperTitle
    data-slot="stepper-title"
    v-bind="forwardedProps"
    :class="cn('text-xs font-medium text-muted-foreground data-[state=active]:text-foreground data-[state=completed]:text-foreground', props.class)"
  >
    <slot />
  </StepperTitle>
</template>
