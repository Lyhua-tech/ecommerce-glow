<script setup lang="ts">
import {
    destroy,
    showAll,
    update,
} from '@/actions/App/Http/Controllers/CartController';
import { index } from '@/actions/App/Http/Controllers/ProductController';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Products',
        href: index.url(),
    },
    {
        title: 'Checkout',
        href: showAll.url(),
    },
];

const props = defineProps({
    carts: {
        type: Object,
        required: true,
    },
});

const total = computed(() => {
    return props.carts
        .reduce((acc: any, value: any) => {
            const price = value.product.price;
            return acc + price * value.quantity;
        }, 0)
        .toFixed(2);
});

const updateQuantity = (cart: any, newQuantity: number) => {
    // Prevent quantity from going below 1
    if (newQuantity < 1) {
        return;
    }

    router.put(
        update.url(cart.product_id),
        {
            quantity: newQuantity,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};

const removeItem = (productId: number) => {
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }

    router.delete(destroy.url(productId), {
        preserveScroll: true,
        preserveState: true,
    });
};

const getItemPrice = (product: any) => {
    return product.sale_price && product.sale_price > 0
        ? product.sale_price
        : product.price;
};
</script>

<template>
    <Head title="Checkout" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <!-- Use a grid layout for cart and summary -->
        <div class="container mx-auto p-4 md:p-10">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Column 1: Cart Items -->
                <div class="lg:col-span-2">
                    <h1 class="mb-6 text-2xl font-semibold">Shopping Cart</h1>

                    <div v-if="props.carts.length > 0" class="space-y-4">
                        <div
                            v-for="cart in props.carts"
                            :key="cart.id"
                            class="flex flex-col items-center rounded-lg border bg-white p-4 shadow-sm md:flex-row"
                        >
                            <!-- Product Image -->
                            <img
                                :src="
                                    cart.product.images[0]?.image_url ||
                                    'https://placehold.co/100x100/f1f1f1/ccc?text=Img'
                                "
                                alt="product image"
                                class="mb-4 h-24 w-24 rounded-md object-cover md:mr-6 md:mb-0"
                            />

                            <!-- Product Details -->
                            <div class="mr-4 min-w-0 flex-1">
                                <Link
                                    :href="index.url(cart.product.id)"
                                    class="truncate text-lg font-medium text-gray-800 hover:text-blue-600"
                                >
                                    {{ cart.product.name }}
                                </Link>
                                <p class="text-sm text-gray-500">
                                    SKU: {{ cart.product.sku }}
                                </p>
                                <p
                                    class="mt-1 text-lg font-semibold text-gray-900"
                                >
                                    ${{ getItemPrice(cart.product) }}
                                </p>
                            </div>

                            <!-- Quantity Controls -->
                            <div
                                class="my-4 flex items-center space-x-3 md:mx-6 md:my-0"
                            >
                                <button
                                    @click="
                                        updateQuantity(cart, cart.quantity - 1)
                                    "
                                    :disabled="cart.quantity <= 1"
                                    class="rounded-md bg-gray-200 px-3 py-1 text-gray-800 hover:bg-gray-300 disabled:opacity-50"
                                >
                                    -
                                </button>
                                <span class="w-10 text-center font-medium">{{
                                    cart.quantity
                                }}</span>
                                <button
                                    @click="
                                        updateQuantity(cart, cart.quantity + 1)
                                    "
                                    class="rounded-md bg-gray-200 px-3 py-1 text-gray-800 hover:bg-gray-300"
                                >
                                    +
                                </button>
                            </div>

                            <!-- Subtotal and Remove -->
                            <div class="flex items-center">
                                <span
                                    class="w-24 text-right text-lg font-semibold text-gray-800"
                                >
                                    ${{
                                        (
                                            getItemPrice(cart.product) *
                                            cart.quantity
                                        ).toFixed(2)
                                    }}
                                </span>
                                <button
                                    @click="removeItem(cart.product.id)"
                                    class="ml-4 text-gray-500 hover:text-red-600"
                                    title="Remove item"
                                >
                                    <!-- Simple text remove, or use an SVG icon -->
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div
                        v-else
                        class="rounded-lg border bg-white p-6 text-center shadow-sm"
                    >
                        <p class="text-gray-600">Your cart is empty.</p>
                        <Link
                            :href="index.url()"
                            class="mt-4 inline-block text-blue-600 hover:underline"
                        >
                            Continue Shopping
                        </Link>
                    </div>
                </div>

                <!-- Column 2: Order Summary -->
                <div class="lg:col-span-1">
                    <div
                        class="sticky top-24 rounded-lg border bg-white p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-xl font-semibold">
                            Order Summary
                        </h2>
                        <div
                            class="mb-4 flex items-center justify-between text-lg font-medium"
                        >
                            <span>Subtotal</span>
                            <span class="text-gray-900">${{ total }}</span>
                        </div>
                        <p class="mb-6 text-sm text-gray-500">
                            Shipping and taxes will be calculated at checkout.
                        </p>
                        <Button
                            class="w-full rounded-lg py-3 font-semibold text-white"
                        >
                            Proceed to Checkout
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
