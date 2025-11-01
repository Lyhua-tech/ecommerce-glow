<script setup lang="ts">
import {
    create as productCreate,
    show,
} from '@/actions/App/Http/Controllers/ProductController';
import { Card, CardContent } from '@/components/ui/card';
import type { CarouselApi } from '@/components/ui/carousel';
import {
    Carousel,
    CarouselContent,
    CarouselItem,
    CarouselNext,
    CarouselPrevious,
} from '@/components/ui/carousel';
import { watchOnce } from '@vueuse/core';

import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useCartStore } from '@/store/cartStore';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const user = computed(() => usePage().props.auth.user);

const isAdmin = computed(() => {
    const role = (user.value as any)?.role;
    return role === 'admin';
});

const cartStore = useCartStore();

const props = defineProps<{
    product: {
        id: number;
        name: string;
        description: string;
        images: [{ id: number; image_url: string }];
        price: number;
        sale_price?: number | null;
        sale_start_date?: string | null;
        sale_end_date?: string | null;
        sku: string;
        color_code: string;
        user_id: number;
        category: { name: string };
    };
}>();

const emblaMainApi = ref<CarouselApi>();
const emblaThumbnailApi = ref<CarouselApi>();
const selectedIndex = ref(0);

function onSelect() {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    selectedIndex.value = emblaMainApi.value.selectedScrollSnap();
    emblaThumbnailApi.value.scrollTo(emblaMainApi.value.selectedScrollSnap());
}

function onThumbClick(index: number) {
    if (!emblaMainApi.value || !emblaThumbnailApi.value) return;
    emblaMainApi.value.scrollTo(index);
}

watchOnce(emblaMainApi, (emblaMainApi) => {
    if (!emblaMainApi) return;

    onSelect();
    emblaMainApi.on('select', onSelect);
    emblaMainApi.on('reInit', onSelect);
});

const breadcrumbs = computed<BreadcrumbItem[]>(() => [
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'View',
        href: show.url(props.product),
    },
]);

// 1. A computed property to check if the product is actively on sale
const isSaleActive = computed(() => {
    const now = new Date();
    const start = props.product.sale_start_date
        ? new Date(props.product.sale_start_date)
        : null;
    const end = props.product.sale_end_date
        ? new Date(props.product.sale_end_date)
        : null;

    // Must have a valid sale price
    if (!props.product.sale_price || props.product.sale_price <= 0) {
        return false;
    }

    // Check dates (this logic handles all cases)
    if (start && end) return now >= start && now <= end;
    if (start && !end) return now >= start;
    if (!start && end) return now <= end;
    if (!start && !end) return true; // Permanent sale if no dates

    return false;
});

// 3. A function for your "Add to Cart" button
function addToCart() {
    if (props.product) {
        cartStore.addToCart(props.product, 1,);
    }
    console.log(`Adding product ${props.product.id} to cart.`);
}
</script>

<template>
    <Head :title="props.product.name" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6 lg:p-8">
            <div class="flex justify-start" v-if="isAdmin">
                <Link :href="productCreate.url()">
                    <Button variant="outline">Create a New Product</Button>
                </Link>
            </div>

            <div class="mx-auto w-full max-w-7xl">
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:gap-12">
                    <div class="flex items-start">
                        <div class="w-full sm:w-auto">
                            <Carousel
                                class="relative w-full max-w-xs"
                                @init-api="(val) => (emblaMainApi = val)"
                            >
                                <CarouselContent>
                                    <CarouselItem
                                        v-for="image in props.product.images"
                                        :key="image.id"
                                    >
                                        <div class="p-1">
                                            <Card>
                                                <CardContent
                                                    class="flex aspect-square items-center justify-center p-6"
                                                >
                                                    <img
                                                        :src="image.image_url"
                                                        alt="image_url"
                                                    />
                                                </CardContent>
                                            </Card>
                                        </div>
                                    </CarouselItem>
                                </CarouselContent>
                                <CarouselPrevious />
                                <CarouselNext />
                            </Carousel>

                            <Carousel
                                class="relative w-full max-w-xs"
                                @init-api="(val) => (emblaThumbnailApi = val)"
                            >
                                <CarouselContent class="ml-0 flex gap-1">
                                    <CarouselItem
                                        v-for="image in props.product.images"
                                        :key="image.id"
                                        class="basis-1/4 cursor-pointer pl-0"
                                        @click="onThumbClick(image.id)"
                                    >
                                        <div
                                            class="p-1"
                                            :class="
                                                image.id === selectedIndex
                                                    ? ''
                                                    : 'opacity-50'
                                            "
                                        >
                                            <Card>
                                                <CardContent
                                                    class="flex aspect-square items-center justify-center p-6"
                                                >
                                                    <img
                                                        :src="image.image_url"
                                                        alt="image_url"
                                                    />
                                                </CardContent>
                                            </Card>
                                        </div>
                                    </CarouselItem>
                                </CarouselContent>
                            </Carousel>
                        </div>
                    </div>

                    <div class="flex flex-col">
                        <!-- <Badge class="mb-2 w-fit">{{
                            props.product.category.name
                        }}</Badge> -->

                        <h1
                            class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl"
                        >
                            {{ props.product.name }}
                        </h1>

                        <div class="mt-4">
                            <span class="sr-only">Price</span>
                            <div
                                v-if="isSaleActive"
                                class="flex items-baseline gap-2"
                            >
                                <span class="text-3xl font-bold text-red-600">
                                    ${{ props.product.sale_price }}
                                </span>
                                <span
                                    class="text-xl font-medium text-gray-500 line-through"
                                >
                                    ${{ props.product.price }}
                                </span>
                            </div>
                            <div v-else>
                                <span class="text-3xl font-bold text-gray-900">
                                    ${{ props.product.price }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h3 class->Description</h3>
                            <p class="mt-2 text-base text-gray-700">
                                {{ props.product.description }}
                            </p>
                        </div>

                        <div class="mt-8">
                            <Button size="lg" class="w-full" @click="addToCart">
                                Add to Cart
                            </Button>
                        </div>

                        <div class="mt-8 border-t border-gray-200 pt-6">
                            <h3 class="text-sm font-medium text-gray-900">
                                Product Details
                            </h3>
                            <ul class="mt-2 space-y-2 text-sm text-gray-600">
                                <li>
                                    <span class="font-medium">SKU:</span>
                                    {{ props.product.sku }}
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="font-medium">Color:</span>
                                    <span
                                        class="size-5 rounded-full border"
                                        :style="{
                                            backgroundColor:
                                                props.product.color_code,
                                        }"
                                    ></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
