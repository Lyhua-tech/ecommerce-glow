<script setup lang="ts">
import {
    destroy,
    showAll,
} from '@/actions/App/Http/Controllers/CartController';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import type { BreadcrumbItemType } from '@/types';
import type { User as UserInfo } from '@/types/index';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ShoppingCart, Trash, User } from 'lucide-vue-next';
import { computed } from 'vue';
import DropDownHead from './DropDownHead.vue';
import Button from './ui/button/Button.vue';
import DropdownMenuItem from './ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from './ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuSeparator from './ui/dropdown-menu/DropdownMenuSeparator.vue';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
    }>(),
    {
        breadcrumbs: () => [],
    },
);

interface ProductImage {
    image_url: string;
}

interface Product {
    name: string;
    price: number;
    images: ProductImage[];
}

interface CartItem {
    id: number;
    quantity: number;
    product: Product;
}

const user = computed(() => usePage().props.auth.user as UserInfo | null);
const carts = computed(() => usePage().props.carts as CartItem[]);
const cartCount = computed(() => usePage().props.cart_count as number);

const isUser = computed(() => {
    const role = (user.value as any)?.role;

    return role === 'user';
});

const removeFromCart = (item: number) => {
    // cartStore.removeCartItem(item);
    router.delete(destroy.url(item), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex w-full items-center justify-between">
            <div class="flex items-center gap-2">
                <SidebarTrigger class="-ml-1" />
                <template v-if="breadcrumbs && breadcrumbs.length > 0">
                    <Breadcrumbs :breadcrumbs="breadcrumbs" />
                </template>
            </div>

            <div class="flex items-center gap-2">
                <div v-if="isUser" class="size-[30px]">
                    <DropDownHead>
                        <template #trigger>
                            <div class="group relative">
                                <Button
                                    variant="ghost"
                                    class="size-full rounded-full p-2 has-[>svg]:p-2"
                                >
                                    <ShoppingCart />
                                </Button>

                                <span
                                    class="absolute -top-1 -right-1 z-[100] flex h-[20px] min-w-[20px] items-center justify-center rounded-full bg-white text-[12px] font-medium transition-all duration-200 group-hover:bg-black group-hover:text-white"
                                    :class="
                                        cartCount === 0
                                            ? 'text-red-500'
                                            : 'text-black'
                                    "
                                >
                                    {{ cartCount > 99 ? '99+' : cartCount }}
                                </span>
                            </div>
                        </template>
                        <template #content>
                            <div
                                v-if="cartCount === 0"
                                class="h-[100px] w-[200px] text-center"
                            >
                                <p class="text-center">Your cart is empty.</p>
                            </div>
                            <div v-else class="w-72">
                                <!-- Header for the dropdown -->
                                <DropdownMenuLabel
                                    >My Cart ({{
                                        cartCount
                                    }})</DropdownMenuLabel
                                >
                                <DropdownMenuSeparator />

                                <!-- Add a wrapper for scrolling if the list gets long -->
                                <div class="max-h-[300px] overflow-y-auto p-2">
                                    <!-- Loop through items -->
                                    <div
                                        v-for="(item, index) in carts"
                                        :key="item.id"
                                    >
                                        <!-- The Redesigned Item -->
                                        <div
                                            class="flex items-center gap-3 py-2"
                                        >
                                            <!-- Image -->
                                            <img
                                                class="size-12 shrink-0 rounded-md object-cover"
                                                :src="
                                                    item.product.images[0]
                                                        ?.image_url
                                                "
                                                :alt="item.product.name"
                                            />

                                            <!-- Info -->
                                            <div class="flex-1 overflow-hidden">
                                                <p
                                                    class="truncate text-sm font-medium"
                                                >
                                                    {{ item.product.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-muted-foreground"
                                                >
                                                    {{ item.quantity }} &times;
                                                    ${{ item.product.price }}
                                                </p>
                                            </div>

                                            <!-- Remove Button -->
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="size-8 shrink-0 rounded-full"
                                                @click="removeFromCart(item.id)"
                                            >
                                                <Trash class="size-4" />
                                            </Button>
                                        </div>

                                        <!-- Separator line, but not after the last item -->
                                        <DropdownMenuSeparator
                                            v-if="index < carts.length - 1"
                                        />
                                    </div>

                                    <Link :href="showAll.url()">
                                        <Button class="w-full"
                                            >Check out</Button
                                        >
                                    </Link>
                                </div>
                            </div>
                        </template>
                    </DropDownHead>
                </div>
                <div v-if="isUser" class="size-[30px]">
                    <DropDownHead>
                        <template #trigger>
                            <Button
                                variant="ghost"
                                class="size-full rounded-full p-2 has-[>svg]:p-2"
                            >
                                <User />
                            </Button>
                        </template>
                        <template #content>
                            <DropdownMenuLabel>My Account</DropdownMenuLabel>
                            <DropdownMenuSeparator />
                            <DropdownMenuItem>Profile</DropdownMenuItem>
                            <DropdownMenuItem>Billing</DropdownMenuItem>
                            <DropdownMenuItem>Team</DropdownMenuItem>
                            <DropdownMenuItem>Subscription</DropdownMenuItem>
                        </template>
                    </DropDownHead>
                </div>
            </div>
        </div>
    </header>
</template>
