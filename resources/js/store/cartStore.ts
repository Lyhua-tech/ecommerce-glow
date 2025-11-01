import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

import { type CartItem, type Product } from '@/types';

export const useCartStore = defineStore('cart', () => {
    const items = ref<CartItem[]>([]);

    const cartCount = computed(() => {
        return items.value.reduce((total, item) => total + item.quantity, 0);
    });

    async function fetchCart() {
        try {
            const response = await axios.get('/carts');
            items.value = response.data;
        } catch (error) {
            console.error('failed to fetch cart', error);
        }
    }

    async function addToCart(product: Product, quantity = 1) {
        try {
            await axios.post('/add-to-cart', {
                product_id: product.id,
                quantity: quantity,
            });

            await fetchCart();
        } catch (error) {
            console.error('Fail to add to cart', error);
        }
    }

    async function removeCartItem(cartId: number) {
        try {
            await axios.delete(`/carts/${cartId}`);

            items.value = items.value.filter((item) => item.id !== cartId);
        } catch (error) {
            console.error('Failed to remove from cart:', error);
        }
    }

    // async function addQuantity(productId: number){
    //     try {
            
    //     } catch (error) {
            
    //     }
    // }

    return { cartCount, fetchCart, addToCart, items, removeCartItem };
});
