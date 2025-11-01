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

import { edit, update } from '@/actions/App/Http/Controllers/ProductController';
import FormFieldInput from '@/components/FormFieldInput.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { toTypedSchema } from '@vee-validate/zod';
import { Cloud } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { computed, ref, watch } from 'vue';
import * as z from 'zod';

const props = defineProps<{
    product: { id: number; [key: string]: any };
}>();

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Edit',
        href: edit.url(props.product),
    },
]);

const existingImageUrl = ref<string | null>(null);

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
            )
            .optional()
            .nullable(),
        description: z
            .string()
            .min(30, 'description must be at least 30')
            .max(250, 'description must not be longer than 250 characters'),
        category_id: z.coerce.number().min(1, 'Please pick one option.'),
        sku: z.string().min(10, 'sku is required.'),
        sale_price: z.coerce.number().positive().nullable().optional(),
        sale_start_date: z.coerce.date().optional().nullable(),
        sale_end_date: z.coerce.date().optional().nullable(),
    }),
);

const formatDate = (dateString: string | null | undefined | Date) => {
    if (!dateString) {
        return undefined;
    }

    const date = new Date(dateString);

    if (isNaN(date.getTime())) {
        return undefined;
    }

    const year = date.getFullYear();

    const month = (date.getMonth() + 1).toString().padStart(2, '0');

    const day = date.getDate().toString().padStart(2, '0');

    return `${year}-${month}-${day}`;
};
const { handleSubmit, resetForm, values, setFieldValue } = useForm({
    validationSchema: formSchema,
    initialValues: {
        name: '',
        price: 0,
        color_code: '',
        image_url: undefined,
        sku: '',
        description: '',
        category_id: 0,
        sale_price: 0,
        sale_start_date: undefined,
        sale_end_date: undefined,
    },
});

watch(
    () => props.product,
    (newProduct) => {
        if (newProduct) {
            resetForm({
                values: {
                    name: newProduct.name,
                    price: newProduct.price,
                    sku: newProduct.sku,
                    description: newProduct.description,
                    color_code: newProduct.color_code,
                    image_url: undefined,
                    sale_price: newProduct.sale_price,
                    category_id: newProduct?.category_id?.toString(),
                    sale_start_date: newProduct.sale_start_date
                        ? new Date(newProduct.sale_start_date)
                        : undefined,
                    sale_end_date: newProduct.sale_end_date
                        ? new Date(newProduct.sale_end_date)
                        : undefined,
                },
            });
            if (newProduct.image_url) {
                existingImageUrl.value = `/storage/${newProduct.image_url}`;
            } else {
                existingImageUrl.value = null;
            }
        }
    },
    { immediate: true },
);

const previewSrc = computed(() => {
    if (values.image_url instanceof File) {
        return URL.createObjectURL(values.image_url);
    }
    if (existingImageUrl.value) {
        return existingImageUrl.value;
    }

    return undefined;
});

const removeImage = () => {
    setFieldValue('image_url', undefined);
    existingImageUrl.value = null;
};

const submit = handleSubmit((values) => {
    const url = update.url(props.product);

    const data = {
        ...values,
        _method: 'PUT',
    };

    router.post(url, data, { forceFormData: true });
});
</script>

<template>
    <Head title="Edit" />

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
                    <div class="w-full">
                        <FormField
                            v-slot="{ field }"
                            name="sale_start_date"
                            class="w-full"
                        >
                            <FormItem>
                                <FormLabel>Sale Start:</FormLabel>
                                <FormControl>
                                    <input
                                        type="date"
                                        :value="formatDate(field.value)"
                                        @input="
                                            field.onChange(
                                                (
                                                    $event.target as HTMLInputElement
                                                ).value,
                                            )
                                        "
                                        class="w-full rounded border px-2 py-1"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>

                    <div class="w-full">
                        <FormField
                            v-slot="{ field }"
                            name="sale_end_date"
                            class="w-full"
                        >
                            <FormItem>
                                <FormLabel>Sale End:</FormLabel>
                                <FormControl>
                                    <input
                                        type="date"
                                        :value="formatDate(field.value)"
                                        @input="
                                            field.onChange(
                                                (
                                                    $event.target as HTMLInputElement
                                                ).value,
                                            )
                                        "
                                        class="w-full rounded border px-2 py-1"
                                    />
                                </FormControl>
                                <FormMessage />
                            </FormItem>
                        </FormField>
                    </div>
                </div>

                <FormField v-slot="{ componentField, value }" name="image_url">
                    <FormItem>
                        <FormLabel>Image</FormLabel>
                        <div v-if="value || existingImageUrl" class="mb-4">
                            <img
                                :src="previewSrc"
                                alt="Image Preview"
                                class="size-32 rounded-md object-cover"
                            />

                            <Button
                                type="button"
                                variant="destructive"
                                size="sm"
                                class="mt-2"
                                @click.prevent="removeImage"
                            >
                                Remove Image
                            </Button>
                        </div>
                        <FormControl v-else>
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
