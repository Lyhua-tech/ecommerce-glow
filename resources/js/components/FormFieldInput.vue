<script lang="ts" setup>
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

import { Input } from '@/components/ui/input';

const props = defineProps({
    name: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        required: true,
    },
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div class="w-full">
        <FormField v-slot="{ componentField }" :name="props.name">
            <FormItem>
                <FormLabel>{{ props.label }}</FormLabel>
                <FormControl>
                    <Input
                        :type="props.type"
                        :placeholder="props.placeholder"
                        v-bind="componentField"
                    />
                </FormControl>
                <FormMessage />
            </FormItem>
        </FormField>
    </div>
</template>
