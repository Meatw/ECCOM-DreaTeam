<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { fetchUserOrders } from '$lib/api';
    import { isAuthenticated } from '$lib/auth';
    import type { Order } from '$lib/types';
    
    let orders: Order[] = [];
    let isLoading = true;
    
    onMount(async () => {
      // Check if user is authenticated
      if (!isAuthenticated()) {
        goto('/login?redirect=orders');
        return;
      }
      
      try {
        orders = await fetchUserOrders();
      } catch (error) {
        console.error('Error fetching orders:', error);
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
    
    function viewOrderDetails(orderId: number) {
      goto(`/orders/${orderId}`);
    }
  </script>
  
  <svelte:head>
    <title>My Orders - QuickBuy</title>
  </svelte:head>
  
  <div class="orders-page">
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
      <div class="orders-content">
        <h1>My Orders</h1>
        
        {#if isLoading}
          <div class="loading-container">
            <div class="spinner"></div>
            <p>Loading your orders...</p>
          </div>
        {:else if orders.length === 0}
          <div class="empty-orders">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M22 12H16L14 15H10L8 12H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M5.45 5.11L2 12V18C2 18.5304 2.21071 19.0391 2.58579 19.4142C2.96086 19.7893 3.46957 20 4 20H20C20.5304 20 21.0391 19.7893 21.4142 19.4142C21.7893 19.0391 22 18.5304 22 18V12L18.55 5.11C18.3844 4.77679 18.1292 4.49637 17.813 4.30028C17.4967 4.10419 17.1321 4.0002 16.76 4H7.24C6.86792 4.0002 6.50326 4.10419 6.18704 4.30028C5.87083 4.49637 5.61558 4.77679 5.45 5.11Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h2>No Orders Yet</h2>
            <p>You haven't placed any orders yet. Start shopping to place your first order!</p>
            <a href="/products" class="shop-now-button">Shop Now</a>
          </div>
        {:else}
          <div class="orders-list">
            {#each orders as order}
              <div class="order-card">
                <div class="order-header">
                  <div class="order-info">
                    <div class="order-id">Order #{order.id}</div>
                    <div class="order-date">{formatDate(order.createdAt)}</div>
                  </div>
                  <div class="order-status">
                    <span class={getStatusClass(order.status)}>
                      {order.status.charAt(0).toUpperCase() + order.status.slice(1)}
                    </span>
                  </div>
                </div>
                
                <div class="order-items">
                  {#each order.items.slice(0, 2) as item}
                    <div class="order-item">
                      <div class="item-image">
                        <img src={item.product.imageUrl || "/placeholder.svg"} alt={item.product.name} />
                      </div>
                      <div class="item-details">
                        <div class="item-name">{item.product.name}</div>
                        <div class="item-meta">
                          <span class="item-quantity">Qty: {item.quantity}</span>
                          <span class="item-price">{formatPrice(item.product.price)}</span>
                        </div>
                      </div>
                    </div>
                  {/each}
                  
                  {#if order.items.length > 2}
                    <div class="more-items">
                      +{order.items.length - 2} more items
                    </div>
                  {/if}
                </div>
                
                <div class="order-footer">
                  <div class="order-total">
                    <span>Total:</span>
                    <span>{formatPrice(order.totalAmount)}</span>
                  </div>
                  <button class="view-details-button" on:click={() => viewOrderDetails(order.id)}>
                    View Details
                  </button>
                </div>
              </div>
            {/each}
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
    .orders-page {
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
    
    .orders-content {
      padding: 2rem 0;
    }
    
    .orders-content h1 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 2rem;
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
    
    .empty-orders {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 4rem 0;
      text-align: center;
    }
    
    .empty-orders svg {
      color: #9ca3af;
      margin-bottom: 1.5rem;
    }
    
    .empty-orders h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .empty-orders p {
      color: #6b7280;
      margin-bottom: 1.5rem;
      max-width: 400px;
    }
    
    .shop-now-button {
      display: inline-block;
      background-color: #000;
      color: white;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      text-decoration: none;
      transition: background-color 0.2s;
    }
    
    .shop-now-button:hover {
      background-color: #333;
    }
    
    .orders-list {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
    }
    
    .order-card {
      background-color: white;
      border-radius: 0.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
      overflow: hidden;
    }
    
    .order-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background-color: #f9fafb;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .order-id {
      font-weight: 600;
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
    
    .order-items {
      padding: 1rem;
    }
    
    .order-item {
      display: flex;
      gap: 1rem;
      padding: 0.5rem 0;
    }
    
    .item-image {
      width: 4rem;
      height: 4rem;
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
      margin-bottom: 0.25rem;
    }
    
    .item-meta {
      display: flex;
      justify-content: space-between;
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .more-items {
      font-size: 0.875rem;
      color: #6b7280;
      text-align: center;
      padding: 0.5rem 0;
      border-top: 1px dashed #e5e7eb;
      margin-top: 0.5rem;
    }
    
    .order-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      background-color: #f9fafb;
      border-top: 1px solid #e5e7eb;
    }
    
    .order-total {
      font-weight: 600;
    }
    
    .view-details-button {
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
    
    .view-details-button:hover {
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
  