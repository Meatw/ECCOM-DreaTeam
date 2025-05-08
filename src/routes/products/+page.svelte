<script lang="ts">
  import { onMount } from 'svelte';
  import { fetchProducts } from '$lib/api';
  import { addToCart } from '$lib/cart';
  import type { Product } from '$lib/types';
  
  let products: Product[] = [];
  let totalProducts = 0;
  let isLoading = true;
  let currentPage = 1;
  let totalPages = 1;
  let searchQuery = '';
  let selectedCategory = '';
  let sortBy = 'newest';
  
  // Categories for filter
  const categories = [
    'All Categories',
    'Electronics',
    'Clothing',
    'Home & Kitchen',
    'Beauty',
    'Books'
  ];
  
  // Sort options
  const sortOptions = [
    { value: 'newest', label: 'Newest' },
    { value: 'price-asc', label: 'Price: Low to High' },
    { value: 'price-desc', label: 'Price: High to Low' },
    { value: 'rating', label: 'Highest Rated' }
  ];
  
  onMount(async () => {
    await loadProducts();
  });
  
  async function loadProducts() {
    isLoading = true;
    
    try {
      const result = await fetchProducts({
        category: selectedCategory === 'All Categories' ? '' : selectedCategory,
        search: searchQuery,
        sortBy,
        page: currentPage,
        limit: 12
      });
      
      products = result.products;
      totalProducts = result.total;
      totalPages = Math.ceil(totalProducts / 12);
    } catch (error) {
      console.error('Error loading products:', error);
    } finally {
      isLoading = false;
    }
  }
  
  function handleSearch() {
    currentPage = 1;
    loadProducts();
  }
  
  function handleCategoryChange(category: string) {
    selectedCategory = category;
    currentPage = 1;
    loadProducts();
  }
  
  function handleSortChange(event: Event) {
    sortBy = (event.target as HTMLSelectElement).value;
    currentPage = 1;
    loadProducts();
  }
  
  function goToPage(page: number) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadProducts();
  }
  
  function formatPrice(price: number): string {
    return `₱${price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
  }
  
  function handleAddToCart(product: Product) {
    addToCart(product, 1);
    
    // Show a toast or notification
    alert(`${product.name} added to cart!`);
  }
</script>

<svelte:head>
  <title>Products - QuickBuy</title>
</svelte:head>

<div class="products-page">
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
      
      <div class="search-bar">
        <input 
          type="text" 
          placeholder="Search products..." 
          bind:value={searchQuery}
          on:keydown={(e) => e.key === 'Enter' && handleSearch()}
        />
        <button class="search-button" on:click={handleSearch}>
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
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
    <div class="products-content">
      <aside class="sidebar">
        <div class="sidebar-section">
          <h3>Categories</h3>
          <ul class="category-list">
            {#each categories as category}
              <li>
                <button 
                  class={selectedCategory === category ? 'active' : ''}
                  on:click={() => handleCategoryChange(category)}
                >
                  {category}
                </button>
              </li>
            {/each}
          </ul>
        </div>
      </aside>
      
      <main class="main-content">
        <div class="products-header">
          <h1>Products</h1>
          
          <div class="products-filters">
            <div class="results-count">
              {totalProducts} results
            </div>
            
            <div class="sort-filter">
              <label for="sort">Sort by:</label>
              <select id="sort" on:change={handleSortChange} value={sortBy}>
                {#each sortOptions as option}
                  <option value={option.value}>{option.label}</option>
                {/each}
              </select>
            </div>
          </div>
        </div>
        
        {#if isLoading}
          <div class="loading-container">
            <div class="spinner"></div>
            <p>Loading products...</p>
          </div>
        {:else if products.length === 0}
          <div class="no-products">
            <h2>No Products Found</h2>
            <p>Try adjusting your search or filter criteria.</p>
          </div>
        {:else}
          <div class="products-grid">
            {#each products as product}
              <div class="product-card">
                <a href="/products/{product.id}" class="product-image">
                  <img src={product.imageUrl || "/placeholder.svg"} alt={product.name} />
                </a>
                <div class="product-details">
                  <a href="/products/{product.id}" class="product-name">
                    {product.name}
                  </a>
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
                    </div>
                    <div class="product-seller">
                      {product.sellerName}
                    </div>
                  </div>
                  <div class="product-price">
                    {formatPrice(product.price)}
                  </div>
                  <button 
                    class="add-to-cart-button" 
                    on:click={() => handleAddToCart(product)}
                    disabled={product.stock <= 0}
                  >
                    {product.stock > 0 ? 'Add to Cart' : 'Out of Stock'}
                  </button>
                </div>
              </div>
            {/each}
          </div>
          
          {#if totalPages > 1}
            <div class="pagination">
              <button 
                class="pagination-button" 
                on:click={() => goToPage(currentPage - 1)}
                disabled={currentPage === 1}
              >
                Previous
              </button>
              
              {#each Array(totalPages) as _, i}
                {#if i + 1 === 1 || i + 1 === totalPages || (i + 1 >= currentPage - 1 && i + 1 <= currentPage + 1)}
                  <button 
                    class={`pagination-button ${currentPage === i + 1 ? 'active' : ''}`}
                    on:click={() => goToPage(i + 1)}
                  >
                    {i + 1}
                  </button>
                {:else if i + 1 === currentPage - 2 || i + 1 === currentPage + 2}
                  <span class="pagination-ellipsis">...</span>
                {/if}
              {/each}
              
              <button 
                class="pagination-button" 
                on:click={() => goToPage(currentPage + 1)}
                disabled={currentPage === totalPages}
              >
                Next
              </button>
            </div>
          {/if}
        {/if}
      </main>
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
  .products-page {
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
    gap: 1rem;
  }
  
  .logo a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    text-decoration: none;
    font-weight: 600;
  }
  
  .search-bar {
    flex: 1;
    max-width: 500px;
    display: flex;
    position: relative;
  }
  
  .search-bar input {
    width: 100%;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 0.875rem;
  }
  
  .search-button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
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
  
  .products-content {
    display: flex;
    gap: 2rem;
    padding: 2rem 0;
  }
  
  .sidebar {
    width: 250px;
    flex-shrink: 0;
  }
  
  @media (max-width: 768px) {
    .products-content {
      flex-direction: column;
    }
    
    .sidebar {
      width: 100%;
    }
  }
  
  .sidebar-section {
    margin-bottom: 2rem;
  }
  
  .sidebar-section h3 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
  }
  
  .category-list {
    list-style: none;
    padding: 0;
    margin: 0;
  }
  
  .category-list li {
    margin-bottom: 0.5rem;
  }
  
  .category-list button {
    background: none;
    border: none;
    padding: 0.5rem 0;
    font-size: 0.875rem;
    color: #6b7280;
    cursor: pointer;
    text-align: left;
    width: 100%;
    transition: color 0.2s;
  }
  
  .category-list button:hover {
    color: #000;
  }
  
  .category-list button.active {
    color: #000;
    font-weight: 600;
  }
  
  .main-content {
    flex: 1;
  }
  
  .products-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
  }
  
  .products-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
  }
  
  .products-filters {
    display: flex;
    align-items: center;
    gap: 1.5rem;
  }
  
  .results-count {
    font-size: 0.875rem;
    color: #6b7280;
  }
  
  .sort-filter {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .sort-filter label {
    font-size: 0.875rem;
    color: #6b7280;
  }
  
  .sort-filter select {
    padding: 0.25rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.875rem;
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
  
  .no-products {
    text-align: center;
    padding: 4rem 0;
  }
  
  .no-products h2 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
  }
  
  .no-products p {
    color: #6b7280;
  }
  
  .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
  }
  
  .product-card {
    background-color: white;
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  
  .product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }
  
  .product-image {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f9fafb;
    overflow: hidden;
  }
  
  .product-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
  }
  
  .product-details {
    padding: 1rem;
  }
  
  .product-name {
    font-weight: 500;
    color: #000;
    text-decoration: none;
    display: block;
    margin-bottom: 0.5rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    height: 2.5rem;
  }
  
  .product-meta {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
  }
  
  .product-rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .stars {
    display: flex;
    color: #d1d5db;
  }
  
  .star {
    font-size: 0.875rem;
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
    font-size: 0.75rem;
    font-weight: 500;
  }
  
  .product-seller {
    font-size: 0.75rem;
    color: #6b7280;
  }
  
  .product-price {
    font-weight: 600;
    margin-bottom: 0.75rem;
  }
  
  .add-to-cart-button {
    width: 100%;
    background-color: #000;
    color: white;
    border: none;
    border-radius: 0.25rem;
    padding: 0.5rem 0;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .add-to-cart-button:hover {
    background-color: #333;
  }
  
  .add-to-cart-button:disabled {
    background-color: #9ca3af;
    cursor: not-allowed;
  }
  
  .pagination {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-top: 2rem;
  }
  
  .pagination-button {
    background-color: white;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .pagination-button:hover {
    background-color: #f3f4f6;
  }
  
  .pagination-button.active {
    background-color: #000;
    color: white;
    border-color: #000;
  }
  
  .pagination-button:disabled {
    color: #9ca3af;
    cursor: not-allowed;
  }
  
  .pagination-ellipsis {
    display: flex;
    align-items: center;
    padding: 0 0.25rem;
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
