<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { login } from '@/routes';

defineOptions({
    layout: {
        title: 'Verify your phone',
        description: 'Enter the 6-digit code we sent by SMS to finish setting up your account.',
    },
});

defineProps<{
    status?: string;
    phoneLastFour?: string | null;
}>();
</script>

<template>
    <Head title="Phone verification" />

    <div
        v-if="status"
        class="mb-4 text-center text-sm font-medium text-green-600"
    >
        {{ status }}
    </div>

    <p class="text-center text-sm text-muted-foreground">
        <template v-if="phoneLastFour">
            We sent a code to a number ending in
            <span class="font-medium text-foreground">{{ phoneLastFour }}</span
            >.
        </template>
        <template v-else> We sent a 6-digit code to your mobile number. </template>
    </p>

    <Form
        action="/register/verify-phone"
        method="post"
        class="mt-6 flex flex-col gap-6"
        reset-on-success
        v-slot="{ errors, processing }"
    >
        <div class="grid gap-2">
            <Label for="otp_code">Verification code</Label>
            <Input
                id="otp_code"
                name="otp_code"
                type="text"
                inputmode="numeric"
                maxlength="6"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="000000"
                class="text-center font-mono text-lg tracking-[0.5em]"
            />
            <InputError :message="errors.otp_code" />
        </div>

        <Button
            type="submit"
            class="w-full"
            :disabled="processing"
            data-test="verify-phone-submit"
        >
            <Spinner v-if="processing" />
            Verify phone
        </Button>
    </Form>

    <Form
        action="/register/verify-phone/resend"
        method="post"
        class="mt-2"
        v-slot="{ processing }"
    >
        <Button
            type="submit"
            variant="secondary"
            class="w-full"
            :disabled="processing"
        >
            <Spinner v-if="processing" />
            Resend code
        </Button>
    </Form>

    <div class="mt-6 text-center text-sm text-muted-foreground">
        Wrong number?
        <TextLink :href="login()" class="underline underline-offset-4"
            >Log out and register again</TextLink
        >
    </div>
</template>
