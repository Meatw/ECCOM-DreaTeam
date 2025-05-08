<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  
  interface Business {
    id: number;
    name: string;
    rating: number;
    location: string;
    category: string;
    description: string;
    image: string;
  }
  
  let featuredBusinesses: Business[] = [];
  
  onMount(async () => {
    try {
      // In a real app, this would fetch from your PHP backend
      // const response = await fetch('/api/businesses/featured');
      // featuredBusinesses = await response.json();
      
      // For now, we'll use mock data
      featuredBusinesses = [
        {
          id: 1,
          name: 'Green Grocery Market',
          rating: 4.8,
          location: 'Downtown',
          category: 'Grocery',
          description: 'Fresh local produce and organic goods from local farms',
          image: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/neonbrand-394691-unsplash-740x710.jpg-XnNSbuKuxx7BYqNetE5YcTk7uoJPko.jpeg'
        },
        {
          id: 2,
          name: 'Fresh Bakery',
          rating: 4.9,
          location: 'Downtown',
          category: 'Bakery',
          description: 'Artisanal breads and pastries baked fresh daily',
          image: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/shutterstock_1544878508.jpg.644x0_q85.jpg-kl7dz5qsXOyEo4Xv5n9Pz60IewYW3A.jpeg'
        },
        {
          id: 3,
          name: 'Tech Haven',
          rating: 4.8,
          location: 'Downtown',
          category: 'Electronics',
          description: 'Smart home devices and cutting-edge kitchen tech',
          image: 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-727Sn6g0P9UcdpDbNIi5SBvzvhBQ9K.png'
        }
      ];
    } catch (error) {
      console.error('Error fetching featured businesses:', error);
    }
  });
  
  function handleBrowseProducts() {
    goto('/products');
  }
  
  function handleExploreBusinesses() {
    goto('/businesses');
  }
</script>

<svelte:head>
  <title>QuickBuy - Shop Local, Support Community</title>
</svelte:head>

<div class="app">
  <!-- Header -->
  <header class="header">
    <div class="container header-container">
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
        <input type="text" placeholder="Search your product or business" />
        <button class="search-button">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
      </div>
      
      <div class="nav-icons">
        <a href="/cart" class="cart-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 22C9.55228 22 10 21.5523 10 21C10 20.4477 9.55228 20 9 20C8.44772 20 8 20.4477 8 21C8 21.5523 8.44772 22 9 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M20 22C20.5523 22 21 21.5523 21 21C21 20.4477 20.5523 20 20 20C19.4477 20 19 20.4477 19 21C19 21.5523 19.4477 22 20 22Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M1 1H5L7.68 14.39C7.77144 14.8504 8.02191 15.264 8.38755 15.5583C8.75318 15.8526 9.2107 16.009 9.68 16H19.4C19.8693 16.009 20.3268 15.8526 20.6925 15.5583C21.0581 15.264 21.3086 14.8504 21.4 14.39L23 6H6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="cart-count">0</span>
        </a>
        <a href="/login" class="account-icon">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M12 11C14.2091 11 16 9.20914 16 7C16 4.79086 14.2091 3 12 3C9.79086 3 8 4.79086 8 7C8 9.20914 9.79086 11 12 11Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <h1>Shop Local, Support Community</h1>
        <p>Discover unique products from local businesses in your community.<br>Quality good, delivery fast.</p>
        <div class="hero-buttons">
          <button class="primary-button" on:click={handleBrowseProducts}>Browse Products</button>
          <button class="secondary-button" on:click={handleExploreBusinesses}>Explore Businesses</button>
        </div>
      </div>
    </section>

    <!-- Featured Businesses Section -->
    <section class="featured-section">
      <div class="container">
        <h2>Featured Local Businesses</h2>
        <p class="section-subtitle">Discover and support the best businesses in your community</p>
        
        <div class="business-cards">
          {#each featuredBusinesses as business}
            <div class="business-card">
              <img src={business.image || "/placeholder.svg?height=180&width=300"} alt={business.name} class="business-image" />
              <div class="business-info">
                <div class="business-header">
                  <h3>{business.name}</h3>
                  <div class="rating">
                    <span class="star">★</span>
                    <span>{business.rating}</span>
                  </div>
                </div>
                <div class="business-meta">
                  <span class="location">📍 {business.location}</span>
                  <span class="category">🏷️ {business.category}</span>
                </div>
                <p class="business-description">{business.description}</p>
                <a href={`/businesses/${business.id}`} class="view-button">View Business</a>
              </div>
            </div>
          {/each}
        </div>
        
        <div class="pagination-controls">
          <button class="prev-button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M19 12H5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 19L5 12L12 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button class="next-button">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M12 5L19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer -->
  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
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
          <p>Connecting local businesses with customers in their community.</p>
        </div>
        
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><a href="/categories">Categories</a></li>
            <li><a href="/businesses">Featured Businesses</a></li>
            <li><a href="/trending">Trending Products</a></li>
            <li><a href="/deals">Deals</a></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><a href="/start-selling">Start Selling</a></li>
            <li><a href="/seller-guidelines">Seller Guidelines</a></li>
            <li><a href="/seller-dashboard">Seller Dashboard</a></li>
            <li><a href="/success-stories">Success Stories</a></li>
          </ul>
        </div>
        
        <div class="footer-section">
          <h3>Shop</h3>
          <ul>
            <li><a href="/support">Customer Support</a></li>
            <li><a href="/returns">Return & Refunds</a></li>
            <li><a href="/shipping">Shipping Information</a></li>
            <li><a href="/faq">FAQ</a></li>
          </ul>
        </div>
      </div>
      
      <div class="footer-bottom">
        <p>© 2023 QuickBuy. All rights reserved.</p>
        <div class="footer-links">
          <a href="/terms">Terms</a>
          <a href="/privacy">Privacy</a>
          <a href="/cookies">Cookies</a>
        </div>
      </div>
    </div>
  </footer>
</div>

<style>
  .app {
    font-family: 'Inter', sans-serif;
    color: #333;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }
  
  .container {
    width: 100%;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
  }
  
  /* Header Styles */
  .header {
    background-color: #000;
    color: white;
    padding: 1rem 0;
  }
  
  .header-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  
  .logo a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.25rem;
  }
  
  .search-bar {
    display: flex;
    align-items: center;
    background-color: #f0f0f0;
    border-radius: 9999px;
    padding: 0.5rem 1rem;
    width: 100%;
    max-width: 400px;
  }
  
  .search-bar input {
    border: none;
    background: transparent;
    width: 100%;
    outline: none;
    padding: 0.25rem 0;
  }
  
  .search-button {
    background: none;
    border: none;
    cursor: pointer;
    color: #666;
  }
  
  .nav-icons {
    display: flex;
    gap: 1rem;
  }
  
  .cart-icon, .account-icon {
    position: relative;
    color: white;
  }
  
  .cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #ff4d4f;
    color: white;
    font-size: 0.75rem;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  
  /* Main Content Styles */
  .main-content {
    flex: 1;
    background-color: white;
  }
  
  /* Hero Section */
  .hero-section {
    text-align: center;
    padding: 4rem 1rem;
  }
  
  .hero-section h1 {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
  }
  
  .hero-section p {
    font-size: 1.125rem;
    color: #666;
    margin-bottom: 2rem;
    line-height: 1.5;
  }
  
  .hero-buttons {
    display: flex;
    gap: 1rem;
    justify-content: center;
  }
  
  .primary-button {
    background-color: #111827;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .primary-button:hover {
    background-color: #1f2937;
  }
  
  .secondary-button {
    background-color: white;
    color: #111827;
    padding: 0.75rem 1.5rem;
    border-radius: 0.375rem;
    font-weight: 500;
    border: 1px solid #d1d5db;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .secondary-button:hover {
    background-color: #f9fafb;
  }
  
  /* Featured Section */
  .featured-section {
    padding: 4rem 0;
    background-color: #f9fafb;
  }
  
  .featured-section h2 {
    font-size: 2rem;
    font-weight: 700;
    text-align: center;
    margin-bottom: 0.5rem;
  }
  
  .section-subtitle {
    text-align: center;
    color: #666;
    margin-bottom: 2rem;
  }
  
  .business-cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .business-card {
    border-radius: 0.5rem;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s;
    background-color: white;
  }
  
  .business-card:hover {
    transform: translateY(-4px);
  }
  
  .business-image {
    width: 100%;
    height: 180px;
    object-fit: cover;
  }
  
  .business-info {
    padding: 1rem;
  }
  
  .business-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
  }
  
  .business-header h3 {
    font-weight: 600;
    font-size: 1.125rem;
  }
  
  .rating {
    display: flex;
    align-items: center;
    gap: 0.25rem;
  }
  
  .star {
    color: #f59e0b;
  }
  
  .business-meta {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
    color: #666;
  }
  
  .business-description {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 1rem;
    line-height: 1.4;
  }
  
  .view-button {
    display: inline-block;
    padding: 0.5rem 1rem;
    background-color: white;
    color: #111827;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    text-decoration: none;
    text-align: center;
    transition: background-color 0.2s;
  }
  
  .view-button:hover {
    background-color: #f9fafb;
  }
  
  .pagination-controls {
    display: flex;
    justify-content: center;
    gap: 1rem;
  }
  
  .prev-button, .next-button {
    background-color: white;
    border: 1px solid #d1d5db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .prev-button:hover, .next-button:hover {
    background-color: #f9fafb;
  }
  
  /* Footer Styles */
  .footer {
    background-color: #000;
    color: white;
    padding: 3rem 0 1rem;
  }
  
  .footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
  }
  
  .footer-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
    font-size: 1.25rem;
  }
  
  .footer-section h3 {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 1rem;
  }
  
  .footer-section ul {
    list-style: none;
    padding: 0;
  }
  
  .footer-section li {
    margin-bottom: 0.5rem;
  }
  
  .footer-section a {
    color: #d1d5db;
    text-decoration: none;
    transition: color 0.2s;
  }
  
  .footer-section a:hover {
    color: white;
  }
  
  .footer-bottom {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid #333;
    font-size: 0.875rem;
    color: #d1d5db;
  }
  
  .footer-links {
    display: flex;
    gap: 1.5rem;
  }
  
  .footer-links a {
    color: #d1d5db;
    text-decoration: none;
  }
  
  .footer-links a:hover {
    color: white;
  }
  
  /* Responsive Adjustments */
  @media (max-width: 768px) {
    .hero-section h1 {
      font-size: 2rem;
    }
    
    .hero-buttons {
      flex-direction: column;
      gap: 0.75rem;
    }
    
    .footer-content {
      grid-template-columns: 1fr;
    }
    
    .footer-bottom {
      flex-direction: column;
      gap: 1rem;
      text-align: center;
    }
  }
</style>
