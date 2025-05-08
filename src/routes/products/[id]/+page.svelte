<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { page } from '$app/stores';
    import { fetchProductById, fetchBusinessById } from '$lib/api';
    import { addToCart } from '$lib/cart';
    import type { Product, Business } from '$lib/types';
    import { get } from 'svelte/store';
    
    let product: Product | null = null;
    let business: Business | null = null;
    let isLoading = true;
    let quantity = 1;
    let addingToCart = false;
    let addedToCart = false;
    
    onMount(async () => {
      try {
        const productId = parseInt(get(page).params.id);
        product = await fetchProductById(productId);
        
        if (product) {
          business = await fetchBusinessById(product.sellerId);
        }
      } catch (error) {
        console.error('Error fetching product:', error);
      } finally {
        isLoading = false;
      }
    });
    
    function handleAddToCart() {
      if (!product) return;
      
      addingToCart = true;
      
      // Add to cart
      addToCart(product, quantity);
      
      // Show success message
      addedToCart = true;
      
      // Reset after 3 seconds
      setTimeout(() => {
        addingToCart = false;
        addedToCart = false;
      }, 3000);
    }
    
    function goToBusinessPage() {
      if (!business) return;
      goto(`/business/${business.id}`);
    }
    
    function formatPrice(price: number): string {
      return `₱${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    function decreaseQuantity() {
      if (quantity > 1) {
        quantity--;
      }
    }
    
    function increaseQuantity() {
      if (product && quantity < product.stock) {
        quantity++;
      }
    }
  </script>
  
  <svelte:head>
    <title>{product ? `${product.name} - QuickBuy` : 'Product - QuickBuy'}</title>
  </svelte:head>
  
  <div class="product-detail-page">
    <header class="header">
      <div class="container">
        <div class="logo">
          <a href="/">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 4H4V16H20V4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 20H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>QuickBuy</span>
          </a>
        </div>
        
        <div class="header-actions">
          <a href="/cart" class="cart-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Cart</span>
          </a>
          
          <a href="/login" class="login-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Login</span>
          </a>
        </div>
      </div>
    </header>
    
    <div class="container">
      <div class="breadcrumbs">
        <a href="/">Home</a> &gt; 
        <a href="/products">Products</a> &gt; 
        {#if product}
          <a href="/products?category={product.category}">{product.category}</a> &gt; 
          <span>{product.name}</span>
        {:else}
          <span>Loading...</span>
        {/if}
      </div>
      
      {#if isLoading}
        <div class="loading-container">
          <div class="spinner"></div>
          <p>Loading product details...</p>
        </div>
      {:else if !product}
        <div class="error-container">
          <h2>Product Not Found</h2>
          <p>Sorry, the product you're looking for doesn't exist or has been removed.</p>
          <a href="/products" class="back-button">Back to Products</a>
        </div>
      {:else}
        <div class="product-detail">
          <div class="product-image">
            <img src={product.imageUrl || "/placeholder.svg"} alt={product.name} />
          </div>
          
          <div class="product-info">
            <h1 class="product-name">{product.name}</h1>
            
            <div class="product-meta">
              <div class="product-rating">
                <span class="stars">
                  {#each Array(5) as _, i}
                    {#if i < Math.floor(product.rating)}
                      <span class="star filled">★</span>
                    {:else if i < Math.ceil(product.rating) && product.rating % 1 !== 0}
                      <span class="star half">★</span>
                    {:else}
                      <span class="star">★</span>
                    {/if}
                  {/each}
                </span>
                <span class="rating-value">{product.rating.toFixed(1)}</span>
                <span class="review-count">({product.reviewCount} reviews)</span>
              </div>
              
              <div class="product-seller">
                Sold by: 
                <button class="seller-link" on:click={goToBusinessPage}>
                  {product.sellerName}
                </button>
              </div>
            </div>
            
            <div class="product-price">
              {formatPrice(product.price)}
            </div>
            
            <div class="product-description">
              <h3>Description</h3>
              <p>{product.description}</p>
            </div>
            
            <div class="product-stock">
              <span class={product.stock > 0 ? 'in-stock' : 'out-of-stock'}>
                {product.stock > 0 ? `In Stock (${product.stock} available)` : 'Out of Stock'}
              </span>
            </div>
            
            <div class="product-actions">
              <div class="quantity-selector">
                <button class="quantity-button" on:click={decreaseQuantity} disabled={quantity <= 1}>-</button>
                <input type="number" bind:value={quantity} min="1" max={product.stock} />
                <button class="quantity-button" on:click={increaseQuantity} disabled={quantity >= product.stock}>+</button>
              </div>
              
              <button 
                class="add-to-cart-button" 
                on:click={handleAddToCart} 
                disabled={addingToCart || product.stock <= 0}
              >
                {#if addingToCart}
                  Adding...
                {:else if addedToCart}
                  Added to Cart!
                {:else}
                  Add to Cart
                {/if}
              </button>
            </div>
            
            {#if business}
              <div class="business-preview">
                <h3>About the Seller</h3>
                <div class="business-card">
                  <div class="business-logo">
                    <img src={business.logo || "/placeholder.svg"} alt={business.name} />
                  </div>
                  <div class="business-info">
                    <h4>{business.name}</h4>
                    <div class="business-rating">
                      <span class="stars">
                        {#each Array(5) as _, i}
                          {#if i < Math.floor(business.rating)}
                            <span class="star filled">★</span>
                          {:else if i < Math.ceil(business.rating) && business.rating % 1 !== 0}
                            <span class="star half">★</span>
                          {:else}
                            <span class="star">★</span>
                          {/if}
                        {/each}
                      </span>
                      <span class="rating-value">{business.rating.toFixed(1)}</span>
                      <span class="review-count">({business.reviewCount} reviews)</span>
                    </div>
                    <p class="business-description">{business.description.substring(0, 100)}...</p>
                    <button class="visit-store-button" on:click={goToBusinessPage}>
                      Visit Store
                    </button>
                  </div>
                </div>
              </div>
            {/if}
          </div>
        </div>
      {/if}
    </div>
    
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 4H4V16H20V4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 20H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>QuickBuy</span>
          </div>
          <p>&copy; 2023 QuickBuy. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
  
  <style>
    .product-detail-page {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    
    .header {
      background-color: #000;
      color: white;
      padding: 1rem 0;
    }
    
    .container {
      width: 100%;
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 1rem;
    }
    
    .header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo a {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: white;
      text-decoration: none;
      font-weight: 600;
    }
    
    .header-actions {
      display: flex;
      gap: 1.5rem;
    }
    
    .cart-link, .login-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: white;
      text-decoration: none;
    }
    
    .breadcrumbs {
      margin: 1.5rem 0;
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .breadcrumbs a {
      color: #6b7280;
      text-decoration: none;
    }
    
    .breadcrumbs a:hover {
      text-decoration: underline;
    }
    
    .loading-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 4rem 0;
    }
    
    .spinner {
      width: 3rem;
      height: 3rem;
      border: 3px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      border-top-color: #000;
      animation: spin 1s ease-in-out infinite;
      margin-bottom: 1rem;
    }
    
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
    
    .error-container {
      text-align: center;
      padding: 4rem 0;
    }
    
    .back-button {
      display: inline-block;
      background-color: #000;
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 0.25rem;
      text-decoration: none;
      margin-top: 1.5rem;
    }
    
    .product-detail {
      display: grid;
      grid-template-columns: 1fr;
      gap: 2rem;
      margin-bottom: 3rem;
    }
    
    @media (min-width: 768px) {
      .product-detail {
        grid-template-columns: 1fr 1fr;
      }
    }
    
    .product-image {
      display: flex;
      justify-content: center;
      align-items: center;
      background-color: #f9fafb;
      border-radius: 0.5rem;
      overflow: hidden;
    }
    
    .product-image img {
      max-width: 100%;
      max-height: 400px;
      object-fit: contain;
    }
    
    .product-name {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .product-meta {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-bottom: 1rem;
    }
    
    @media (min-width: 640px) {
      .product-meta {
        flex-direction: row;
        justify-content: space-between;
      }
    }
    
    .product-rating {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .stars {
      display: flex;
      color: #d1d5db;
    }
    
    .star {
      font-size: 1.25rem;
    }
    
    .star.filled {
      color: #f59e0b;
    }
    
    .star.half {
      position: relative;
      color: #d1d5db;
    }
    
    .star.half::before {
      content: "★";
      position: absolute;
      color: #f59e0b;
      width: 50%;
      overflow: hidden;
    }
    
    .rating-value {
      font-weight: 600;
    }
    
    .review-count {
      color: #6b7280;
    }
    
    .product-seller {
      color: #6b7280;
    }
    
    .seller-link {
      background: none;
      border: none;
      color: #000;
      font-weight: 500;
      cursor: pointer;
      padding: 0;
      text-decoration: underline;
    }
    
    .product-price {
      font-size: 1.5rem;
      font-weight: 600;
      margin: 1.5rem 0;
    }
    
    .product-description {
      margin-bottom: 1.5rem;
    }
    
    .product-description h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .product-description p {
      color: #4b5563;
      line-height: 1.5;
    }
    
    .product-stock {
      margin-bottom: 1.5rem;
    }
    
    .in-stock {
      color: #10b981;
      font-weight: 500;
    }
    
    .out-of-stock {
      color: #ef4444;
      font-weight: 500;
    }
    
    .product-actions {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    @media (min-width: 640px) {
      .product-actions {
        flex-direction: row;
        align-items: center;
      }
    }
    
    .quantity-selector {
      display: flex;
      align-items: center;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      overflow: hidden;
    }
    
    .quantity-button {
      background-color: #f3f4f6;
      border: none;
      color: #000;
      font-size: 1.25rem;
      width: 2.5rem;
      height: 2.5rem;
      cursor: pointer;
    }
    
    .quantity-button:disabled {
      color: #9ca3af;
      cursor: not-allowed;
    }
    
    .quantity-selector input {
      width: 3rem;
      height: 2.5rem;
      border: none;
      text-align: center;
      font-size: 1rem;
      -moz-appearance: textfield;
    }
    
    .quantity-selector input::-webkit-outer-spin-button,
    .quantity-selector input::-webkit-inner-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
    
    .add-to-cart-button {
      background-color: #000;
      color: white;
      border: none;
      border-radius: 0.25rem;
      padding: 0 1.5rem;
      height: 2.5rem;
      font-weight: 500;
      cursor: pointer;
      flex: 1;
      transition: background-color 0.2s;
    }
    
    .add-to-cart-button:hover {
      background-color: #333;
    }
    
    .add-to-cart-button:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
    }
    
    .business-preview {
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 1px solid #e5e7eb;
    }
    
    .business-preview h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .business-card {
      display: flex;
      gap: 1rem;
      background-color: #f9fafb;
      border-radius: 0.5rem;
      padding: 1rem;
    }
    
    .business-logo {
      width: 4rem;
      height: 4rem;
      border-radius: 0.25rem;
      overflow: hidden;
      flex-shrink: 0;
    }
    
    .business-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .business-info {
      flex: 1;
    }
    
    .business-info h4 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .business-rating {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      margin-bottom: 0.5rem;
    }
    
    .business-description {
      color: #4b5563;
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }
    
    .visit-store-button {
      background-color: transparent;
      color: #000;
      border: 1px solid #000;
      border-radius: 0.25rem;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .visit-store-button:hover {
      background-color: rgba(0, 0, 0, 0.05);
    }
    
    .footer {
      background-color: #000;
      color: white;
      padding: 2rem 0;
      margin-top: auto;
    }
    
    .footer-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
    }
    
    .footer-logo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
    }
  </style>
  