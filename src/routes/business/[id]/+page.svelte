<script lang="ts">
    import { onMount } from 'svelte';
    import { page } from '$app/stores';
    import { fetchBusinessById, fetchProductsByBusinessId } from '$lib/api';
    import type { Business, Product } from '$lib/types';
    import { get } from 'svelte/store';
    
    let business: Business | null = null;
    let products: Product[] = [];
    let isLoading = true;
    
    onMount(async () => {
      try {
        const businessId = parseInt(get(page).params.id);
        business = await fetchBusinessById(businessId);
        
        if (business) {
          products = await fetchProductsByBusinessId(business.id);
        }
      } catch (error) {
        console.error('Error fetching business:', error);
      } finally {
        isLoading = false;
      }
    });
    
    function formatPrice(price: number): string {
      return `₱${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
  </script>
  
  <svelte:head>
    <title>{business ? `${business.name} - QuickBuy` : 'Business - QuickBuy'}</title>
  </svelte:head>
  
  <div class="business-page">
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
    
    {#if isLoading}
      <div class="loading-container">
        <div class="spinner"></div>
        <p>Loading business details...</p>
      </div>
    {:else if !business}
      <div class="error-container">
        <h2>Business Not Found</h2>
        <p>Sorry, the business you're looking for doesn't exist or has been removed.</p>
        <a href="/" class="back-button">Back to Home</a>
      </div>
    {:else}
      <div class="business-cover" style="background-image: url({business.coverImage})">
        <div class="container">
          <div class="business-header">
            <div class="business-logo">
              <img src={business.logo || "/placeholder.svg"} alt={business.name} />
            </div>
            <div class="business-info">
              <h1>{business.name}</h1>
              <div class="business-meta">
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
                <div class="business-categories">
                  {#each business.categories as category, i}
                    <span class="category">{category}</span>
                    {#if i < business.categories.length - 1}
                      <span class="separator">•</span>
                    {/if}
                  {/each}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="container">
        <div class="business-content">
          <div class="business-description">
            <h2>About {business.name}</h2>
            <p>{business.description}</p>
            
            <div class="business-contact">
              <div class="contact-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M21 10C21 17 12 23 12 23C12 23 3 17 3 10C3 7.61305 3.94821 5.32387 5.63604 3.63604C7.32387 1.94821 9.61305 1 12 1C14.3869 1 16.6761 1.94821 18.364 3.63604C20.0518 5.32387 21 7.61305 21 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M12 13C13.6569 13 15 11.6569 15 10C15 8.34315 13.6569 7 12 7C10.3431 7 9 8.34315 9 10C9 11.6569 10.3431 13 12 13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{business.address}</span>
              </div>
              <div class="contact-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M22 16.92V19.92C22.0011 20.1985 21.9441 20.4742 21.8325 20.7294C21.7209 20.9845 21.5573 21.2136 21.3521 21.4019C21.1468 21.5901 20.9046 21.7335 20.6407 21.8227C20.3769 21.9119 20.0974 21.9451 19.82 21.92C16.7428 21.5856 13.787 20.5341 11.19 18.85C8.77383 17.3147 6.72534 15.2662 5.19 12.85C3.49998 10.2412 2.44824 7.27097 2.12 4.18C2.09501 3.90347 2.12788 3.62476 2.2165 3.36162C2.30513 3.09849 2.44757 2.85669 2.63477 2.65162C2.82196 2.44655 3.04981 2.28271 3.30379 2.17052C3.55778 2.05833 3.83234 2.00026 4.11 2H7.11C7.59531 1.99522 8.06579 2.16708 8.43376 2.48353C8.80173 2.79999 9.04208 3.23945 9.11 3.72C9.23662 4.68007 9.47145 5.62273 9.81 6.53C9.94455 6.88792 9.97366 7.27691 9.89391 7.65088C9.81415 8.02485 9.62886 8.36811 9.36 8.64L8.09 9.91C9.51356 12.4135 11.5865 14.4864 14.09 15.91L15.36 14.64C15.6319 14.3711 15.9752 14.1858 16.3491 14.1061C16.7231 14.0263 17.1121 14.0554 17.47 14.19C18.3773 14.5286 19.3199 14.7634 20.28 14.89C20.7658 14.9585 21.2094 15.2032 21.5265 15.5775C21.8437 15.9518 22.0122 16.4296 22 16.92Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{business.phoneNumber}</span>
              </div>
              <div class="contact-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M22 6L12 13L2 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>{business.email}</span>
              </div>
            </div>
          </div>
          
          <div class="business-products">
            <h2>Products from {business.name}</h2>
            
            {#if products.length === 0}
              <div class="no-products">
                <p>No products available from this business yet.</p>
              </div>
            {:else}
              <div class="products-grid">
                {#each products as product}
                  <a href="/products/{product.id}" class="product-card">
                    <div class="product-image">
                      <img src={product.imageUrl || "/placeholder.svg"} alt={product.name} />
                    </div>
                    <div class="product-details">
                      <h3 class="product-name">{product.name}</h3>
                      <div class="product-price">{formatPrice(product.price)}</div>
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
                      </div>
                    </div>
                  </a>
                {/each}
              </div>
            {/if}
          </div>
        </div>
      </div>
    {/if}
    
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
    .business-page {
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
    
    .business-cover {
      background-color: #f3f4f6;
      background-size: cover;
      background-position: center;
      position: relative;
      padding: 2rem 0;
    }
    
    .business-cover::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
    }
    
    .business-header {
      position: relative;
      display: flex;
      align-items: center;
      gap: 1.5rem;
      color: white;
    }
    
    .business-logo {
      width: 6rem;
      height: 6rem;
      border-radius: 0.5rem;
      overflow: hidden;
      background-color: white;
      flex-shrink: 0;
    }
    
    .business-logo img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    
    .business-info h1 {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    
    .business-meta {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    @media (min-width: 640px) {
      .business-meta {
        flex-direction: row;
        align-items: center;
        gap: 1.5rem;
      }
    }
    
    .business-rating {
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
      opacity: 0.8;
    }
    
    .business-categories {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .category {
      font-size: 0.875rem;
    }
    
    .separator {
      color: rgba(255, 255, 255, 0.5);
    }
    
    .business-content {
      padding: 2rem 0;
    }
    
    .business-description {
      margin-bottom: 3rem;
    }
    
    .business-description h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .business-description p {
      color: #4b5563;
      line-height: 1.6;
      margin-bottom: 1.5rem;
    }
    
    .business-contact {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1rem;
      margin-top: 1.5rem;
    }
    
    @media (min-width: 640px) {
      .business-contact {
        grid-template-columns: repeat(3, 1fr);
      }
    }
    
    .contact-item {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: #4b5563;
    }
    
    .business-products h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    
    .no-products {
      background-color: #f9fafb;
      padding: 2rem;
      text-align: center;
      border-radius: 0.5rem;
    }
    
    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
    }
    
    .product-card {
      display: block;
      text-decoration: none;
      color: inherit;
      border-radius: 0.5rem;
      overflow: hidden;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    
    .product-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    .product-image {
      height: 200px;
      background-color: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .product-image img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .product-details {
      padding: 1rem;
      background-color: white;
    }
    
    .product-name {
      font-size: 1rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }
    
    .product-price {
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .product-rating {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
    }
    
    .product-rating .stars {
      font-size: 0.875rem;
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
  