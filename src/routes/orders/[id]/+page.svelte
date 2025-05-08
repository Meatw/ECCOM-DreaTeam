<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { page } from '$app/stores';
    import { fetchOrderById } from '$lib/api';
    import { isAuthenticated } from '$lib/auth';
    import type { Order } from '$lib/types';
    import { invalidateAll } from '$app/navigation';
    
    let order: Order | null = null;
    let isLoading = true;
    
    onMount(async () => {
      // Check if user is authenticated
      if (!isAuthenticated()) {
        goto('/login?redirect=orders');
        return;
      }
      
      try {
        const orderId = parseInt($page.params.id);
        order = await fetchOrderById(orderId);
        
        if (!order) {
          // Order not found, redirect to orders page
          goto('/orders');
        }
      } catch (error) {
        console.error('Error fetching order:', error);
      } finally {
        isLoading = false;
      }
    });
    
    function formatPrice(price: number): string {
      return `₱${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    function formatDate(dateString: string): string {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    }
    
    function getStatusClass(status: string): string {
      switch (status) {
        case 'pending':
          return 'status-pending';
        case 'processing':
          return 'status-processing';
        case 'shipped':
          return 'status-shipped';
        case 'delivered':
          return 'status-delivered';
        case 'cancelled':
          return 'status-cancelled';
        default:
          return '';
      }
    }
    
    function getStatusStep(status: string): number {
      switch (status) {
        case 'pending':
          return 1;
        case 'processing':
          return 2;
        case 'shipped':
          return 3;
        case 'delivered':
          return 4;
        case 'cancelled':
          return 0;
        default:
          return 0;
      }
    }
  </script>
  
  <svelte:head>
    <title>Order Details - QuickBuy</title>
  </svelte:head>
  
  <div class="order-details-page">
    <header class="header">
      <div class="container">
        <div class="logo">
          <a href="/">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 4H4V16H20V4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 16V20" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
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
      <div class="order-details-content">
        <div class="breadcrumbs">
          <a href="/">Home</a> &gt; 
          <a href="/orders">My Orders</a> &gt; 
          <span>Order Details</span>
        </div>
        
        {#if isLoading}
          <div class="loading-container">
            <div class="spinner"></div>
            <p>Loading order details...</p>
          </div>
        {:else if !order}
          <div class="error-container">
            <h2>Order Not Found</h2>
            <p>Sorry, the order you're looking for doesn't exist or has been removed.</p>
            <a href="/orders" class="back-button">Back to Orders</a>
          </div>
        {:else}
          <div class="order-details">
            <div class="order-header">
              <div class="order-title">
                <h1>Order #{order.id}</h1>
                <div class="order-date">Placed on {formatDate(order.createdAt)}</div>
              </div>
              <div class="order-status">
                <span class={getStatusClass(order.status)}>
                  {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                </span>
              </div>
            </div>
            
            {#if order.status !== 'cancelled'}
              <div class="order-progress">
                <div class="progress-steps">
                  <div class={`progress-step ${getStatusStep(order.status) >= 1 ? 'active' : ''}`}>
                    <div class="step-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 12H16L14 15H10L8 12H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.45 5.11L2 12V18C2 18.5304 2.21071 19.0391 2.58579 19.4142C2.96086 19.7893 3.46957 20 4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V12L18.55 5.11C18.3844 4.77679 18.1292 4.49637 17.813 4.30028C17.4967 4.10419 17.1321 4.0002 16.76 4H7.24C6.86792 4.0002 6.50326 4.10419 6.18704 4.30028C5.87083 4.49637 5.61558 4.77679 5.45 5.11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="step-label">Order Placed</div>
                  </div>
                  <div class="progress-line"></div>
                  <div class={`progress-step ${getStatusStep(order.status) >= 2 ? 'active' : ''}`}>
                    <div class="step-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 6V12L16 14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="step-label">Processing</div>
                  </div>
                  <div class="progress-line"></div>
                  <div class={`progress-step ${getStatusStep(order.status) >= 3 ? 'active' : ''}`}>
                    <div class="step-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 3H1V16H16V3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 8H20L23 11V16H16V8Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5.5 21C6.88071 21 8 19.8807 8 18.5C8 17.1193 6.88071 16 5.5 16C4.11929 16 3 17.1193 3 18.5C3 19.8807 4.11929 21 5.5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M18.5 21C19.8807 21 21 19.8807 21 18.5C21 17.1193 19.8807 16 18.5 16C17.1193 16 16 17.1193 16 18.5C16 19.8807 17.1193 21 18.5 21Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="step-label">Shipped</div>
                  </div>
                  <div class="progress-line"></div>
                  <div class={`progress-step ${getStatusStep(order.status) >= 4 ? 'active' : ''}`}>
                    <div class="step-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 4L12 14.01L9 11.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="step-label">Delivered</div>
                  </div>
                </div>
              </div>
            {/if}
            
            <div class="order-sections">
              <div class="order-section">
                <h2>Items in Your Order</h2>
                <div class="order-items">
                  {#each order.items as item}
                    <div class="order-item">
                      <div class="item-image">
                        <img src={item.product.imageUrl || "/placeholder.svg"} alt={item.product.name} />
                      </div>
                      <div class="item-details">
                        <a href="/products/{item.product.id}" class="item-name">
                          {item.product.name}
                        </a>
                        <div class="item-seller">
                          Sold by: {item.product.sellerName}
                        </div>
                        <div class="item-meta">
                          <div class="item-price">{formatPrice(item.product.price)}</div>
                          <div class="item-quantity">Quantity: {item.quantity}</div>
                        </div>
                        <div class="item-total">
                          Subtotal: {formatPrice(item.product.price * item.quantity)}
                        </div>
                      </div>
                    </div>
                  {/each}
                </div>
              </div>
              
              <div class="order-info-columns">
                <div class="order-section">
                  <h2>Shipping Information</h2>
                  <div class="info-content">
                    <div class="info-item">
                      <div class="info-label">Address</div>
                      <div class="info-value">{order.shippingAddress}</div>
                    </div>
                  </div>
                </div>
                
                <div class="order-section">
                  <h2>Payment Information</h2>
                  <div class="info-content">
                    <div class="info-item">
                      <div class="info-label">Payment Method</div>
                      <div class="info-value">
                        {order.paymentMethod === 'cod' ? 'Cash on Delivery' : order.paymentMethod}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="order-section">
                <h2>Order Summary</h2>
                <div class="order-summary">
                  <div class="summary-row">
                    <span>Subtotal</span>
                    <span>{formatPrice(order.totalAmount)}</span>
                  </div>
                  <div class="summary-row">
                    <span>Shipping</span>
                    <span>Free</span>
                  </div>
                  <div class="summary-row total">
                    <span>Total</span>
                    <span>{formatPrice(order.totalAmount)}</span>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="order-actions">
              <a href="/orders" class="back-to-orders">
                Back to Orders
              </a>
              
              {#if order.status === 'delivered'}
                <button class="action-button">
                  Write a Review
                </button>
              {/if}
            </div>
          </div>
        {/if}
      </div>
    </div>
    
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M20 4H4V16H20V4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M4 8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M9 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M15 16V20" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
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
    .order-details-page {
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
    
    .order-details-content {
      padding: 2rem 0;
    }
    
    .breadcrumbs {
      margin-bottom: 1.5rem;
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
    
    .order-details {
      background-color: white;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem;
      background-color: #f9fafb;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .order-title h1 {
      font-size: 1.25rem;
      font-weight: 600;
      margin-bottom: 0.25rem;
    }
    
    .order-date {
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .order-status {
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .status-pending {
      color: #f59e0b;
      background-color: rgba(245, 158, 11, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    
    .status-processing {
      color: #3b82f6;
      background-color: rgba(59, 130, 246, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    
    .status-shipped {
      color: #8b5cf6;
      background-color: rgba(139, 92, 246, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    
    .status-delivered {
      color: #10b981;
      background-color: rgba(16, 185, 129, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    
    .status-cancelled {
      color: #ef4444;
      background-color: rgba(239, 68, 68, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
    }
    
    .order-progress {
      padding: 2rem 1.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .progress-steps {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .progress-step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      color: #9ca3af;
    }
    
    .progress-step.active {
      color: #000;
    }
    
    .step-icon {
      width: 3rem;
      height: 3rem;
      border-radius: 50%;
      background-color: #f3f4f6;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .progress-step.active .step-icon {
      background-color: #000;
      color: white;
    }
    
    .step-label {
      font-size: 0.875rem;
      font-weight: 500;
    }
    
    .progress-line {
      flex: 1;
      height: 2px;
      background-color: #e5e7eb;
      margin: 0 0.5rem;
    }
    
    .order-sections {
      padding: 1.5rem;
    }
    
    .order-section {
      margin-bottom: 2rem;
    }
    
    .order-section h2 {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .order-items {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    
    .order-item {
      display: flex;
      gap: 1rem;
    }
    
    .item-image {
      width: 5rem;
      height: 5rem;
      background-color: #f9fafb;
      border-radius: 0.25rem;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .item-image img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .item-details {
      flex: 1;
    }
    
    .item-name {
      font-weight: 500;
      color: #000;
      text-decoration: none;
      display: block;
      margin-bottom: 0.25rem;
    }
    
    .item-name:hover {
      text-decoration: underline;
    }
    
    .item-seller {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }
    
    .item-meta {
      display: flex;
      gap: 1rem;
      font-size: 0.875rem;
      margin-bottom: 0.25rem;
    }
    
    .item-total {
      font-weight: 500;
    }
    
    .order-info-columns {
      display: grid;
      grid-template-columns: 1fr;
      gap: 1.5rem;
    }
    
    @media (min-width: 768px) {
      .order-info-columns {
        grid-template-columns: 1fr 1fr;
      }
    }
    
    .info-content {
      background-color: #f9fafb;
      border-radius: 0.25rem;
      padding: 1rem;
    }
    
    .info-item {
      margin-bottom: 0.5rem;
    }
    
    .info-item:last-child {
      margin-bottom: 0;
    }
    
    .info-label {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.25rem;
    }
    
    .info-value {
      font-weight: 500;
    }
  </style>
  