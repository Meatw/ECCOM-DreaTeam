<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { cart, updateCartItemQuantity, removeFromCart, clearCart } from '$lib/cart';
    import { isAuthenticated } from '$lib/auth';
    
    let isLoading = false;
    let checkoutStep = 1;
    let shippingAddress = '';
    let paymentMethod = 'cod';
    let orderPlaced = false;
    let orderId: number | null = null;
    
    function formatPrice(price: number): string {
      return `₱${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
    
    function handleQuantityChange(productId: number, newQuantity: number) {
      updateCartItemQuantity(productId, newQuantity);
    }
    
    function handleRemoveItem(productId: number) {
      if (confirm('Are you sure you want to remove this item from your cart?')) {
        removeFromCart(productId);
      }
    }
    
    function goToCheckout() {
      if (!isAuthenticated()) {
        goto('/login?redirect=cart');
        return;
      }
      
      checkoutStep = 2;
    }
    
    async function placeOrder() {
      if (!shippingAddress) {
        alert('Please enter your shipping address');
        return;
      }
      
      isLoading = true;
      
      try {
        // In a real app, this would call your PHP backend
        // For demo purposes, we'll simulate an API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Generate a random order ID
        orderId = Math.floor(Math.random() * 10000) + 1;
        
        // Clear the cart
        clearCart();
        
        // Show order confirmation
        orderPlaced = true;
        checkoutStep = 3;
      } catch (error) {
        console.error('Error placing order:', error);
        alert('There was an error placing your order. Please try again.');
      } finally {
        isLoading = false;
      }
    }
    
    function continueShopping() {
      goto('/products');
    }
    
    function viewOrder() {
      goto(`/orders/${orderId}`);
    }
  </script>
  
  <svelte:head>
    <title>Shopping Cart - QuickBuy</title>
  </svelte:head>
  
  <div class="cart-page">
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
          <a href="/products" class="products-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M6 2L3 6V20C3 20.5304 3.21071 21.0391 3.58579 21.4142C3.96086 21.7893 4.46957 22 5 22H19C19.5304 22 20.0391 21.7893 20.4142 21.4142C20.7893 21.0391 21 20.5304 21 20V6L18 2H6Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M3 6H21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16 10C16 11.0609 15.5786 12.0783 14.8284 12.8284C14.0783 13.5786 13.0609 14 12 14C10.9391 14 9.92172 13.5786 9.17157 12.8284C8.42143 12.0783 8 11.0609 8 10" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>Products</span>
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
      <div class="cart-content">
        <div class="cart-header">
          <h1>Shopping Cart</h1>
          
          <div class="checkout-steps">
            <div class={`step ${checkoutStep >= 1 ? 'active' : ''}`}>
              <div class="step-number">1</div>
              <div class="step-label">Cart</div>
            </div>
            <div class="step-connector"></div>
            <div class={`step ${checkoutStep >= 2 ? 'active' : ''}`}>
              <div class="step-number">2</div>
              <div class="step-label">Checkout</div>
            </div>
            <div class="step-connector"></div>
            <div class={`step ${checkoutStep >= 3 ? 'active' : ''}`}>
              <div class="step-number">3</div>
              <div class="step-label">Confirmation</div>
            </div>
          </div>
        </div>
        
        {#if checkoutStep === 1}
          {#if $cart.items.length === 0}
            <div class="empty-cart">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h2>Your cart is empty</h2>
              <p>Looks like you haven't added any products to your cart yet.</p>
              <button class="continue-shopping-button" on:click={continueShopping}>
                Continue Shopping
              </button>
            </div>
          {:else}
            <div class="cart-items">
              <table class="cart-table">
                <thead>
                  <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  {#each $cart.items as item}
                    <tr>
                      <td class="product-cell">
                        <div class="product-info">
                          <div class="product-image">
                            <img src={item.product.imageUrl || "/placeholder.svg"} alt={item.product.name} />
                          </div>
                          <div class="product-details">
                            <a href="/products/{item.product.id}" class="product-name">
                              {item.product.name}
                            </a>
                            <div class="product-seller">
                              Sold by: {item.product.sellerName}
                            </div>
                          </div>
                        </div>
                      </td>
                      <td class="price-cell">
                        {formatPrice(item.product.price)}
                      </td>
                      <td class="quantity-cell">
                        <div class="quantity-selector">
                          <button 
                            class="quantity-button" 
                            on:click={() => handleQuantityChange(item.productId, item.quantity - 1)} 
                            disabled={item.quantity <= 1}
                          >
                            -
                          </button>
                          <input 
                            type="number" 
                            value={item.quantity} 
                            min="1" 
                            max={item.product.stock} 
                            on:change={(e) => handleQuantityChange(item.productId, parseInt(e.target.value))}
                          />
                          <button 
                            class="quantity-button" 
                            on:click={() => handleQuantityChange(item.productId, item.quantity + 1)} 
                            disabled={item.quantity >= item.product.stock}
                          >
                            +
                          </button>
                        </div>
                      </td>
                      <td class="total-cell">
                        {formatPrice(item.product.price * item.quantity)}
                      </td>
                      <td class="action-cell">
                        <button 
                          class="remove-button" 
                          on:click={() => handleRemoveItem(item.productId)}
                          title="Remove item"
                        >
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                          </svg>
                        </button>
                      </td>
                    </tr>
                  {/each}
                </tbody>
              </table>
            </div>
            
            <div class="cart-summary">
              <div class="summary-details">
                <div class="summary-row">
                  <span>Subtotal ({$cart.totalItems} items)</span>
                  <span>{formatPrice($cart.subtotal)}</span>
                </div>
                <div class="summary-row">
                  <span>Shipping</span>
                  <span>Free</span>
                </div>
                <div class="summary-row total">
                  <span>Total</span>
                  <span>{formatPrice($cart.subtotal)}</span>
                </div>
              </div>
              
              <div class="summary-actions">
                <button class="checkout-button" on:click={goToCheckout}>
                  Proceed to Checkout
                </button>
                <button class="continue-shopping-button" on:click={continueShopping}>
                  Continue Shopping
                </button>
              </div>
            </div>
          {/if}
        {:else if checkoutStep === 2}
          <div class="checkout-form">
            <h2>Checkout</h2>
            
            <div class="form-section">
              <h3>Shipping Information</h3>
              <div class="form-group">
                <label for="shippingAddress">Shipping Address</label>
                <textarea 
                  id="shippingAddress" 
                  bind:value={shippingAddress} 
                  placeholder="Enter your complete shipping address" 
                  required
                ></textarea>
              </div>
            </div>
            
            <div class="form-section">
              <h3>Payment Method</h3>
              <div class="payment-options">
                <label class="payment-option">
                  <input type="radio" name="paymentMethod" value="cod" bind:group={paymentMethod} />
                  <div class="payment-option-content">
                    <div class="payment-option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 9V7C17 5.89543 16.1046 5 15 5H5C3.89543 5 3 5.89543 3 7V13C3 14.1046 3.89543 15 5 15H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 9H9C7.89543 9 7 9.89543 7 11V17C7 18.1046 7.89543 19 9 19H19C20.1046 19 21 18.1046 21 17V11C21 9.89543 20.1046 9 19 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="payment-option-details">
                      <div class="payment-option-name">Cash on Delivery</div>
                      <div class="payment-option-description">Pay when you receive your order</div>
                    </div>
                  </div>
                </label>
                
                <label class="payment-option disabled">
                  <input type="radio" name="paymentMethod" value="card" disabled />
                  <div class="payment-option-content">
                    <div class="payment-option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 4H3C1.89543 4 1 4.89543 1 6V18C1 19.1046 1.89543 20 3 20H21C22.1046 20 23 19.1046 23 18V6C23 4.89543 22.1046 4 21 4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1 10H23" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="payment-option-details">
                      <div class="payment-option-name">Credit/Debit Card</div>
                      <div class="payment-option-description">Coming soon</div>
                    </div>
                  </div>
                </label>
                
                <label class="payment-option disabled">
                  <input type="radio" name="paymentMethod" value="gcash" disabled />
                  <div class="payment-option-content">
                    <div class="payment-option-icon">
                      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 9V7C17 5.89543 16.1046 5 15 5H5C3.89543 5 3 5.89543 3 7V13C3 14.1046 3.89543 15 5 15H7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M19 9H9C7.89543 9 7 9.89543 7 11V17C7 18.1046 7.89543 19 9 19H19C20.1046 19 21 18.1046 21 17V11C21 9.89543 20.1046 9 19 9Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </div>
                    <div class="payment-option-details">
                      <div class="payment-option-name">GCash</div>
                      <div class="payment-option-description">Coming soon</div>
                    </div>
                  </div>
                </label>
              </div>
            </div>
            
            <div class="order-summary">
              <h3>Order Summary</h3>
              <div class="summary-items">
                {#each $cart.items as item}
                  <div class="summary-item">
                    <div class="summary-item-name">
                      {item.product.name} x {item.quantity}
                    </div>
                    <div class="summary-item-price">
                      {formatPrice(item.product.price * item.quantity)}
                    </div>
                  </div>
                {/each}
              </div>
              <div class="summary-total">
                <span>Total</span>
                <span>{formatPrice($cart.subtotal)}</span>
              </div>
            </div>
            
            <div class="checkout-actions">
              <button class="back-button" on:click={() => checkoutStep = 1}>
                Back to Cart
              </button>
              <button 
                class="place-order-button" 
                on:click={placeOrder} 
                disabled={isLoading}
              >
                {isLoading ? 'Processing...' : 'Place Order'}
              </button>
            </div>
          </div>
        {:else if checkoutStep === 3}
          <div class="order-confirmation">
            <div class="confirmation-icon">
              <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M22 11.08V12C21.9988 14.1564 21.3005 16.2547 20.0093 17.9818C18.7182 19.709 16.9033 20.9725 14.8354 21.5839C12.7674 22.1953 10.5573 22.1219 8.53447 21.3746C6.51168 20.6273 4.78465 19.2461 3.61096 17.4371C2.43727 15.628 1.87979 13.4881 2.02168 11.3363C2.16356 9.18455 2.99721 7.13631 4.39828 5.49706C5.79935 3.85781 7.69279 2.71537 9.79619 2.24013C11.8996 1.7649 14.1003 1.98232 16.07 2.85999" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M22 4L12 14.01L9 11.01" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h2>Order Placed Successfully!</h2>
            <p class="confirmation-message">
              Thank you for your order. Your order has been placed successfully and will be processed soon.
            </p>
            <div class="order-details">
              <div class="order-detail-item">
                <span>Order ID:</span>
                <span>#{orderId}</span>
              </div>
              <div class="order-detail-item">
                <span>Order Date:</span>
                <span>{new Date().toLocaleDateString()}</span>
              </div>
              <div class="order-detail-item">
                <span>Payment Method:</span>
                <span>{paymentMethod === 'cod' ? 'Cash on Delivery' : paymentMethod}</span>
              </div>
            </div>
            <div class="confirmation-actions">
              <button class="view-order-button" on:click={viewOrder}>
                View Order
              </button>
              <button class="continue-shopping-button" on:click={continueShopping}>
                Continue Shopping
              </button>
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
    .cart-page {
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
    
    .products-link, .login-link {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      color: white;
      text-decoration: none;
    }
    
    .cart-content {
      padding: 2rem 0;
    }
    
    .cart-header {
      margin-bottom: 2rem;
    }
    
    .cart-header h1 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    
    .checkout-steps {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
    }
    
    .step {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
    }
    
    .step-number {
      width: 2rem;
      height: 2rem;
      border-radius: 50%;
      background-color: #f3f4f6;
      color: #6b7280;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
    }
    
    .step.active .step-number {
      background-color: #000;
      color: white;
    }
    
    .step-label {
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .step.active .step-label {
      color: #000;
      font-weight: 600;
    }
    
    .step-connector {
      flex: 1;
      height: 2px;
      background-color: #f3f4f6;
      margin: 0 0.5rem;
      margin-bottom: 1.5rem;
    }
    
    .empty-cart {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 4rem 0;
      text-align: center;
    }
    
    .empty-cart svg {
      color: #9ca3af;
      margin-bottom: 1.5rem;
    }
    
    .empty-cart h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .empty-cart p {
      color: #6b7280;
      margin-bottom: 1.5rem;
    }
    
    .continue-shopping-button {
      background-color: transparent;
      color: #000;
      border: 1px solid #000;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .continue-shopping-button:hover {
      background-color: rgba(0, 0, 0, 0.05);
    }
    
    .cart-items {
      margin-bottom: 2rem;
    }
    
    .cart-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .cart-table th {
      text-align: left;
      padding: 1rem;
      border-bottom: 1px solid #e5e7eb;
      font-weight: 500;
      color: #6b7280;
    }
    
    .cart-table td {
      padding: 1rem;
      border-bottom: 1px solid #e5e7eb;
      vertical-align: middle;
    }
    
    .product-cell {
      width: 40%;
    }
    
    .product-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    
    .product-image {
      width: 5rem;
      height: 5rem;
      background-color: #f9fafb;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 0.25rem;
      overflow: hidden;
    }
    
    .product-image img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
    }
    
    .product-details {
      flex: 1;
    }
    
    .product-name {
      font-weight: 500;
      color: #000;
      text-decoration: none;
      display: block;
      margin-bottom: 0.25rem;
    }
    
    .product-name:hover {
      text-decoration: underline;
    }
    
    .product-seller {
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .price-cell, .total-cell {
      font-weight: 500;
    }
    
    .quantity-cell {
      width: 10rem;
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
      width: 2rem;
      height: 2rem;
      cursor: pointer;
    }
    
    .quantity-button:disabled {
      color: #9ca3af;
      cursor: not-allowed;
    }
    
    .quantity-selector input {
      width: 3rem;
      height: 2rem;
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
    
    .action-cell {
      width: 3rem;
      text-align: center;
    }
    
    .remove-button {
      background-color: transparent;
      border: none;
      color: #6b7280;
      cursor: pointer;
      padding: 0.5rem;
      border-radius: 0.25rem;
      transition: background-color 0.2s;
    }
    
    .remove-button:hover {
      background-color: #f3f4f6;
      color: #ef4444;
    }
    
    .cart-summary {
      background-color: #f9fafb;
      border-radius: 0.5rem;
      padding: 1.5rem;
    }
    
    .summary-details {
      margin-bottom: 1.5rem;
    }
    
    .summary-row {
      display: flex;
      justify-content: space-between;
      padding: 0.75rem 0;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .summary-row.total {
      font-weight: 600;
      font-size: 1.125rem;
      border-bottom: none;
    }
    
    .summary-actions {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .checkout-button {
      background-color: #000;
      color: white;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .checkout-button:hover {
      background-color: #333;
    }
    
    .checkout-form {
      max-width: 800px;
      margin: 0 auto;
    }
    
    .checkout-form h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    
    .form-section {
      margin-bottom: 2rem;
    }
    
    .form-section h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
    }
    
    .form-group input, .form-group textarea {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
    }
    
    .form-group textarea {
      min-height: 6rem;
      resize: vertical;
    }
    
    .payment-options {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }
    
    .payment-option {
      display: flex;
      align-items: flex-start;
      gap: 1rem;
      padding: 1rem;
      border: 1px solid #d1d5db;
      border-radius: 0.5rem;
      cursor: pointer;
      transition: border-color 0.2s;
    }
    
    .payment-option:hover {
      border-color: #9ca3af;
    }
    
    .payment-option.disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }
    
    .payment-option input {
      margin-top: 0.25rem;
    }
    
    .payment-option-content {
      display: flex;
      align-items: center;
      gap: 1rem;
      flex: 1;
    }
    
    .payment-option-icon {
      color: #6b7280;
    }
    
    .payment-option-details {
      flex: 1;
    }
    
    .payment-option-name {
      font-weight: 500;
      margin-bottom: 0.25rem;
    }
    
    .payment-option-description {
      font-size: 0.875rem;
      color: #6b7280;
    }
    
    .order-summary {
      background-color: #f9fafb;
      border-radius: 0.5rem;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }
    
    .order-summary h3 {
      font-size: 1.125rem;
      font-weight: 600;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .summary-items {
      margin-bottom: 1rem;
    }
    
    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 0;
    }
    
    .summary-item-name {
      flex: 1;
    }
    
    .summary-item-price {
      font-weight: 500;
    }
    
    .summary-total {
      display: flex;
      justify-content: space-between;
      padding-top: 1rem;
      border-top: 1px solid #e5e7eb;
      font-weight: 600;
      font-size: 1.125rem;
    }
    
    .checkout-actions {
      display: flex;
      gap: 1rem;
    }
    
    .back-button {
      background-color: transparent;
      color: #000;
      border: 1px solid #000;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .back-button:hover {
      background-color: rgba(0, 0, 0, 0.05);
    }
    
    .place-order-button {
      background-color: #000;
      color: white;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
      flex: 1;
    }
    
    .place-order-button:hover {
      background-color: #333;
    }
    
    .place-order-button:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
    }
    
    .order-confirmation {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
      padding: 2rem;
      background-color: #f9fafb;
      border-radius: 0.5rem;
    }
    
    .confirmation-icon {
      margin-bottom: 1.5rem;
    }
    
    .order-confirmation h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .confirmation-message {
      color: #6b7280;
      margin-bottom: 2rem;
      max-width: 600px;
    }
    
    .order-details {
      width: 100%;
      max-width: 400px;
      margin-bottom: 2rem;
    }
    
    .order-detail-item {
      display: flex;
      justify-content: space-between;
      padding: 0.75rem 0;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .order-detail-item:last-child {
      border-bottom: none;
    }
    
    .confirmation-actions {
      display: flex;
      gap: 1rem;
    }
    
    .view-order-button {
      background-color: #000;
      color: white;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem 1.5rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .view-order-button:hover {
      background-color: #333;
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
  