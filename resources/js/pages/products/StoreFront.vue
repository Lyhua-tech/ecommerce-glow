<script setup lang="ts">
import {
    show as productDetail,
    index as productsIndex,
} from '@/actions/App/Http/Controllers/ProductController';
import Input from '@/components/ui/input/Input.vue';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

import Button from '@/components/ui/button/Button.vue';
import { Card, CardContent, CardFooter, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ShoppingCart } from 'lucide-vue-next';
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

const addToCart = (product: any, quantity = 1) => {
    // Implement your cart logic here
    router.post(
        '/add-to-cart',
        {
            product_id: product.id,
            quantity,
        },
        { preserveScroll: true, preserveState: true },
    );
};
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            <div class="flex items-center justify-start gap-4">
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

            <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <Card v-for="product in props.products.data" :key="product.id">
                    <Link :href="productDetail.url(product)">
                        <div class="aspect-square overflow-hidden bg-gray-100">
                            <img
                                :src="product.images[0]?.image_url"
                                :alt="product.name"
                                class="h-full w-full object-cover transition-transform duration-300 hover:scale-105"
                            />
                        </div>
                        <CardContent class="space-y-3 p-4">
                            <CardTitle class="line-clamp-2 text-lg">
                                {{ product.name }}
                            </CardTitle>

                            <p
                                v-if="product.description"
                                class="line-clamp-2 text-sm text-muted-foreground"
                            >
                                {{ product.description }}
                            </p>
                        </CardContent>
                    </Link>
                    <CardFooter
                        ><div
                            class="flex w-full items-center justify-between pt-2"
                        >
                            <span class="text-2xl font-bold">
                                ${{ product.price || '0.00' }}
                            </span>

                            <Button
                                @click="addToCart(product)"
                                class="gap-2"
                                variant="default"
                            >
                                <ShoppingCart class="h-4 w-4" />
                                Add to Cart
                            </Button>
                        </div>
                    </CardFooter>
                </Card>
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
