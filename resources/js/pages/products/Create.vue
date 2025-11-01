<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    FormControl,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form';

import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import FormFieldInput from '@/components/FormFieldInput.vue';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

import {
    Empty,
    EmptyContent,
    EmptyDescription,
    EmptyHeader,
    EmptyMedia,
    EmptyTitle,
} from '@/components/ui/empty';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Cloud } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import * as z from 'zod';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: '/products',
    },
];

const MAX_FILE_SIZE = 5 * 1024 * 1024;
const ACCEPTED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

const formSchema = toTypedSchema(
    z.object({
        name: z.string().min(3).max(30),
        price: z.coerce.number().positive(),
        color_code: z.string(),
        image_url: z
            .instanceof(File, { message: 'An image is required.' })
            .refine(
                (file) => file.size <= MAX_FILE_SIZE,
                `Max file size is 5MB.`,
            )
            .refine(
                (file) => ACCEPTED_IMAGE_TYPES.includes(file.type),
                'Only .jpg, .png, and .webp formats are supported.',
            ),
        description: z
            .string()
            .min(30, 'description must be at least 30')
            .max(250, 'description must not be longer than 250 characters'),
        category_id: z.coerce.number().min(1, 'Please pick one option.'),
        sku: z.string().min(10, 'sku is required.'),
        sale_price: z.coerce.number().nullable().optional(),
        sale_start_date: z.coerce.date().optional().nullable(),
        sale_end_date: z.coerce.date().optional().nullable(),
    }),
);

const { handleSubmit } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        price: 0,
        color_code: '',
        image_url: undefined,
        sku: '',
        description: '',
        category_id: undefined,
        sale_price: 0,
        sale_start_date: null,
        sale_end_date: null,
    },
});

const submit = handleSubmit((value) => {
    router.post('/products', value);
});
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full w-full flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <form @submit="submit" class="space-y-4">
                <div class="flex w-full gap-3">

                    <FormFieldInput
                        name="name"
                        label="Product Name"
                        type="text"
                        placeholder="My Product"
                    />

                    <FormFieldInput
                        name="sku"
                        label="Stock Keeping Unit"
                        type="text"
                        placeholder="BEER-CAM-001"
                    />

                    <FormFieldInput
                        name="color_code"
                        label="Color"
                        type="text"
                        placeholder="#FFFFFF"
                    />

                    <div class="h-[28px] w-full">
                        <FormField
                            v-slot="{ componentField }"
                            name="category_id"
                        >
                            <FormItem>
                                <FormLabel>Category</FormLabel>
                                <FormControl>
                                    <Select v-bind="componentField">
                                        <SelectTrigger class="w-full">
                                            <SelectValue
                                                placeholder="Select Category"
                                            />
                                        </SelectTrigger>
                                        <SelectContent>
                                            <SelectGroup>
                                                <SelectItem value="1"
                                                    >Devices</SelectItem
                                                >
                                                <SelectItem value="2"
                                                    >Clothes</SelectItem
                                                >
                                                <SelectItem value="3"
                                                    >Accessories</SelectItem
                                                >
                                                <SelectItem value="4"
                                                    >Food</SelectItem
                                                >
                                                <SelectItem value="5"
                                                    >Drink</SelectItem
                                                >
                                            </SelectGroup>
                                        </SelectContent>
                                    </Select>
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>
                </div>
                <FormField
                    v-slot="{ componentField }"
                    name="description"
                    class="w-full"
                >
                    <FormItem>
                        <FormLabel>Description: </FormLabel>
                        <FormControl>
                            <Textarea
                                class=""
                                type="text"
                                placeholder="Describe the product."
                                v-bind="componentField"
                            />
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <div class="flex w-full gap-3">

                    <FormFieldInput
                        name="price"
                        label="Price"
                        type="text"
                        placeholder="9.99"
                    />

                    <FormFieldInput
                        name="sale_price"
                        label="Sale Pice"
                        type="text"
                        placeholder="168.99"
                    />
                </div>

                <div class="flex w-full gap-3">

                    <FormFieldInput
                        name="sale_start_date"
                        label="Sale Start Date"
                        type="date"
                    />

                    <FormFieldInput
                        name="sale_end_date"
                        label="Sale End Date"
                        type="date"
                    />
                </div>

                <FormField v-slot="{ componentField }" name="image_url">
                    <FormItem>
                        <FormLabel>Image</FormLabel>
                        <FormControl>
                            <Empty class="border border-dashed">
                                <EmptyHeader>
                                    <EmptyMedia variant="icon">
                                        <Cloud />
                                    </EmptyMedia>
                                    <EmptyTitle>Cloud Storage Empty</EmptyTitle>
                                    <EmptyDescription>
                                        Upload files to your cloud storage to
                                        access them anywhere.
                                    </EmptyDescription>
                                </EmptyHeader>
                                <EmptyContent>
                                    <Input
                                        type="file"
                                        @change="
                                            (e: Event) => {
                                                const target =
                                                    e.target as HTMLInputElement;
                                                if (target.files) {
                                                    componentField.onChange(
                                                        target.files[0],
                                                    );
                                                }
                                            }
                                        "
                                    />
                                </EmptyContent>
                            </Empty>
                        </FormControl>
                        <FormMessage />
                    </FormItem>
                </FormField>

                <Button type="submit">Save</Button>
            </form>
        </div>
    </AppLayout>
</template>
