import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'user' | 'admin';
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export interface Product {
    id: number;
    name: string;
    description: string;
    sku: string;
    color_code: string;

    // Decimals are cast to strings by Laravel to avoid precision loss
    price: string;
    sale_price: string | null;

    stock: number;
    sale_start_date: string | null; // Timestamps are cast to ISO date strings
    sale_end_date: string | null;

    subcategory_id: number;
    user_id: number;

    created_at: string;
    updated_at: string;

    // You can also add relationships if you eager-load them
    // subcategory?: Subcategory;
    // user?: User;
}

// It's also good practice to type your CartItem
export interface CartItem {
    id: number; // The cart item's own ID
    product_id: number;
    user_id: number;
    quantity: number;
    sub_total: string; // This was also a decimal in your controller
    created_at: string;
    updated_at: string;

    // You should eager-load the product with the cart item
    product: Product;
}

export type BreadcrumbItemType = BreadcrumbItem;
