import { writable, get } from "svelte/store"
import type { Cart, Product } from "./types"

// Initialize cart from localStorage if available
const storedCart = typeof localStorage !== "undefined" ? localStorage.getItem("quickbuy_cart") : null

const initialCart: Cart = storedCart ? JSON.parse(storedCart) : { items: [], totalItems: 0, subtotal: 0 }

// Create cart store
export const cart = writable<Cart>(initialCart)

// Save cart to localStorage whenever it changes
cart.subscribe((value) => {
  if (typeof localStorage !== "undefined") {
    localStorage.setItem("quickbuy_cart", JSON.stringify(value))
  }
})

// Add item to cart
export function addToCart(product: Product, quantity = 1): void {
  cart.update((currentCart) => {
    // Check if product already exists in cart
    const existingItemIndex = currentCart.items.findIndex((item) => item.productId === product.id)

    const updatedItems = [...currentCart.items]

    if (existingItemIndex >= 0) {
      // Update quantity if product already in cart
      updatedItems[existingItemIndex] = {
        ...updatedItems[existingItemIndex],
        quantity: updatedItems[existingItemIndex].quantity + quantity,
      }
    } else {
      // Add new item if product not in cart
      updatedItems.push({
        productId: product.id,
        product,
        quantity,
      })
    }

    // Calculate new totals
    const totalItems = updatedItems.reduce((total, item) => total + item.quantity, 0)

    const subtotal = updatedItems.reduce((total, item) => total + item.product.price * item.quantity, 0)

    return {
      items: updatedItems,
      totalItems,
      subtotal,
    }
  })
}

// Remove item from cart
export function removeFromCart(productId: number): void {
  cart.update((currentCart) => {
    const updatedItems = currentCart.items.filter((item) => item.productId !== productId)

    // Calculate new totals
    const totalItems = updatedItems.reduce((total, item) => total + item.quantity, 0)

    const subtotal = updatedItems.reduce((total, item) => total + item.product.price * item.quantity, 0)

    return {
      items: updatedItems,
      totalItems,
      subtotal,
    }
  })
}

// Update item quantity
export function updateCartItemQuantity(productId: number, quantity: number): void {
  if (quantity <= 0) {
    removeFromCart(productId)
    return
  }

  cart.update((currentCart) => {
    const updatedItems = currentCart.items.map((item) => (item.productId === productId ? { ...item, quantity } : item))

    // Calculate new totals
    const totalItems = updatedItems.reduce((total, item) => total + item.quantity, 0)

    const subtotal = updatedItems.reduce((total, item) => total + item.product.price * item.quantity, 0)

    return {
      items: updatedItems,
      totalItems,
      subtotal,
    }
  })
}

// Clear cart
export function clearCart(): void {
  cart.set({ items: [], totalItems: 0, subtotal: 0 })
}

// Get cart
export function getCart(): Cart {
  return get(cart)
}
