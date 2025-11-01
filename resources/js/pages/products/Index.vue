<script setup lang="ts">
import {
    edit as editProduct,
    create as productCreate,
    destroy as productDestroy,
    index as productsIndex,
    show as showProduct,
} from '@/actions/App/Http/Controllers/ProductController';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';

import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from 'vue';

// 1. Define the props to receive the paginated products object from the controller.
const props = defineProps({
    products: {
        type: Object,
        required: true,
    },
    filter: {
        type: Object,
    },
});

const search = ref(props.filter?.search);
const category_id = ref(props.filter?.category_id);

watch(
    search,
    debounce((value: string) => {
        router.get(
            '/products',
            { search: value },
            {
                preserveState: true,
                replace: true,
            },
        );
    }, 300),
);

watch(
    category_id,
    debounce((value: string) => {
        router.get(
            '/products',
            { category_id: value },
            { preserveState: true, replace: true },
        );
    }, 300),
);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: productsIndex.url(),
    },
];

// Function to handle deleting a product
const deleteProduct = (productId: number) => {
    if (confirm('Are you sure you want to delete this product?')) {
        // Use Inertia's router to send a DELETE request
        router.delete(productDestroy.url(productId));
    }
};
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-start gap-4">
                <Link :href="productCreate.url()">
                    <Button class="w-40">Create a Product</Button>
                </Link>

                <Input
                    v-model="search"
                    placeholder="Search..."
                    class="flex-1"
                />

                <Select v-model="category_id">
                    <SelectTrigger class="w-60">
                        <SelectValue placeholder="Select Category" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem value="1">Men</SelectItem>
                            <SelectItem value="2">Women</SelectItem>
                            <SelectItem value="3">Accessories</SelectItem>
                            <SelectItem value="4">Fragement</SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>

            <div>
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead> No.</TableHead>
                            <TableHead> Product Name </TableHead>

                            <TableHead>Price</TableHead>
                            <TableHead>Category</TableHead>
                            <TableHead> Stock Unit Price </TableHead>
                            <TableHead> Color Code </TableHead>
                            <TableHead> Sale Price </TableHead>
                            <TableHead> Sale Date </TableHead>
                            <TableHead> End Sale Date </TableHead>
                            <TableHead> Image </TableHead>
                            <TableHead> Action</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow
                            v-for="product in props.products.data"
                            :key="product.id"
                        >
                            <TableCell>{{ product.id }}</TableCell>
                            <TableCell class="font-medium">
                                <Link :href="showProduct.url(product.id)">
                                    <p>
                                        {{ product.name }}
                                    </p>
                                </Link>
                            </TableCell>
                            <TableCell>$ {{ product.price }}</TableCell>
                            <TableCell> {{ product.category?.name }}</TableCell>
                            <TableCell>{{ product.sku }}</TableCell>
                            <TableCell>
                                {{
                                    product.color_code.toUpperCase()
                                }}</TableCell
                            >
                            <TableCell>{{
                                product.sale_price ? product.sale_price : 'N/A'
                            }}</TableCell>
                            <TableCell>{{
                                product.sale_start_date
                                    ? product.sale_start_date.split('T')[0]
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell>{{
                                product.sale_end_date
                                    ? product.sale_end_date.split('T')[0]
                                    : 'N/A'
                            }}</TableCell>
                            <TableCell
                                ><img
                                    v-if="product.images.length > 0"
                                    :src="product.images[0].image_url"
                                    :alt="product.name"
                                    class="aspect-video size-[60px] object-contain"
                                />
                            </TableCell>
                            <TableCell>
                                <div
                                    class="flex items-center justify-center gap-2"
                                >
                                    <Button
                                        @click="deleteProduct(product.id)"
                                        variant="destructive"
                                    >
                                        delete
                                    </Button>
                                    <Link :href="editProduct.url(product)">
                                        <Button>edit</Button>
                                    </Link>
                                </div>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>

            <div
                v-if="props.products.last_page > 1"
                class="mt-3 flex justify-end"
            >
                <div class="flex gap-2">
                    <div
                        v-for="(link, index) in props.products.links"
                        :key="index"
                        class="rounded border px-4 py-2 text-sm"
                        :class="{
                            'bg-stone-900 text-white': link.active,
                            'text-grey-500': !link.url,
                        }"
                    >
                        <Link v-if="link.url" :href="link.url">
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else v-html="link.label"></span>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
