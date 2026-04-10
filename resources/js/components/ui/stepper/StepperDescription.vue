<script setup lang="ts">
import type { StepperDescriptionProps } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { reactiveOmit } from '@vueuse/core';
import { StepperDescription, useForwardProps } from 'reka-ui';
import { cn } from '@/lib/utils';

const props = defineProps<
    StepperDescriptionProps & { class?: HTMLAttributes['class'] }
>();

const delegatedProps = reactiveOmit(props, 'class');
const forwardedProps = useForwardProps(delegatedProps);
</script>

<template>
    <StepperDescription
        data-slot="stepper-description"
        v-bind="forwardedProps"
        :class="
            cn(
                'text-xs text-muted-foreground data-[state=active]:text-muted-foreground data-[state=completed]:text-muted-foreground',
                props.class,
            )
        "
    >
        <slot />
    </StepperDescription>
</template>
