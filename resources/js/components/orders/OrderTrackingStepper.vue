<script setup lang="ts">
import { AlertCircle, Box, CheckCircle2, Clock3, PackageCheck, Truck } from 'lucide-vue-next';
import {
    Stepper,
    StepperDescription,
    StepperIndicator,
    StepperItem,
    StepperSeparator,
    StepperTitle,
    StepperTrigger,
} from '@/components/ui/stepper';

const props = defineProps<{
    status: string;
}>();

const steps = [
    { key: 'pending', title: 'Pending', description: 'Order received', icon: Clock3 },
    { key: 'confirmed', title: 'Confirmed', description: 'Order confirmed', icon: CheckCircle2 },
    { key: 'packed', title: 'Packed', description: 'Packed for shipment', icon: Box },
    { key: 'shipped', title: 'Shipped', description: 'On the way', icon: Truck },
    { key: 'out_for_delivery', title: 'Out for Delivery', description: 'Almost there', icon: Truck },
    { key: 'delivered', title: 'Delivered', description: 'Completed', icon: PackageCheck },
    { key: 'received', title: 'Received', description: 'Confirmed by customer', icon: CheckCircle2 },
] as const;

const activeStep = () => {
    const index = steps.findIndex((step) => step.key === props.status);

    return index < 0 ? 1 : index + 1;
};
</script>

<template>
    <div v-if="props.status === 'cancelled'" class="rounded-lg border border-destructive/40 bg-destructive/10 p-4">
        <div class="flex items-center gap-2 text-sm font-medium text-destructive">
            <AlertCircle class="size-4" />
            This order was cancelled.
        </div>
    </div>

    <Stepper v-else :model-value="activeStep()" class="flex w-full items-start gap-2">
        <StepperItem
            v-for="item in steps"
            :key="item.key"
            :step="steps.findIndex((step) => step.key === item.key) + 1"
            class="relative flex w-full flex-col items-center justify-center"
        >
            <StepperTrigger>
                <StepperIndicator class="bg-muted">
                    <component :is="item.icon" class="h-4 w-4" />
                </StepperIndicator>
            </StepperTrigger>
            <StepperSeparator
                v-if="item.key !== steps[steps.length - 1]?.key"
                class="absolute top-4 right-[calc(-50%+8px)] left-[calc(50%+16px)] block h-0.5 rounded-full bg-muted group-data-[state=completed]:bg-primary"
            />
            <div class="mt-2 flex flex-col items-center text-center">
                <StepperTitle>{{ item.title }}</StepperTitle>
                <StepperDescription>{{ item.description }}</StepperDescription>
            </div>
        </StepperItem>
    </Stepper>
</template>
