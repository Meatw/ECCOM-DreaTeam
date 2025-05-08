<script lang="ts">
    import { onMount } from 'svelte';
    import { goto } from '$app/navigation';
    import { isAuthenticated, hasRole, logout } from '$lib/auth';
    
    onMount(() => {
      // Check if user is authenticated and has seller role
      if (!isAuthenticated() || !hasRole('seller')) {
        goto('/unauthorized');
      }
    });
    
    function handleLogout() {
      logout();
    }
  </script>
  
  <svelte:head>
    <title>Seller Dashboard - QuickBuy</title>
  </svelte:head>
  
  <div class="seller-dashboard">
    <header class="dashboard-header">
      <div class="logo">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M20 4H4V16H20V4Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4 8H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 16V20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4 20H20" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h1>QuickBuy Seller</h1>
      </div>
      
      <div class="user-actions">
        <button class="logout-button" on:click={handleLogout}>Logout</button>
      </div>
    </header>
    
    <div class="dashboard-content">
      <aside class="sidebar">
        <nav class="nav-menu">
          <a href="/seller/dashboard" class="nav-item active">Dashboard</a>
          <a href="/seller/products" class="nav-item">Products</a>
          <a href="/seller/orders" class="nav-item">Orders</a>
          <a href="/seller/customers" class="nav-item">Customers</a>
          <a href="/seller/analytics" class="nav-item">Analytics</a>
          <a href="/seller/settings" class="nav-item">Settings</a>
        </nav>
      </aside>
      
      <main class="main-area">
        <h2>Seller Dashboard</h2>
        
        <div class="stats-grid">
          <div class="stat-card">
            <h3>Total Products</h3>
            <p class="stat-value">24</p>
          </div>
          
          <div class="stat-card">
            <h3>Total Orders</h3>
            <p class="stat-value">156</p>
          </div>
          
          <div class="stat-card">
            <h3>Total Revenue</h3>
            <p class="stat-value">₱45,678.90</p>
          </div>
          
          <div class="stat-card">
            <h3>Average Rating</h3>
            <p class="stat-value">4.8</p>
          </div>
        </div>
        
        <div class="action-cards">
          <div class="action-card">
            <h3>Add New Product</h3>
            <p>List a new product to your store</p>
            <button class="action-button">Add Product</button>
          </div>
          
          <div class="action-card">
            <h3>View Orders</h3>
            <p>Check your pending and completed orders</p>
            <button class="action-button">View Orders</button>
          </div>
          
          <div class="action-card">
            <h3>Update Store</h3>
            <p>Update your store information and settings</p>
            <button class="action-button">Update Store</button>
          </div>
        </div>
        
        <div class="recent-section">
          <h3>Recent Orders</h3>
          <table class="data-table">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>#12345</td>
                <td>John Doe</td>
                <td>2023-05-15</td>
                <td>₱1,299.99</td>
                <td><span class="status-delivered">Delivered</span></td>
                <td><button class="view-button">View</button></td>
              </tr>
              <tr>
                <td>#12344</td>
                <td>Jane Smith</td>
                <td>2023-05-14</td>
                <td>₱2,499.50</td>
                <td><span class="status-processing">Processing</span></td>
                <td><button class="view-button">View</button></td>
              </tr>
              <tr>
                <td>#12343</td>
                <td>Robert Johnson</td>
                <td>2023-05-14</td>
                <td>₱899.00</td>
                <td><span class="status-shipped">Shipped</span></td>
                <td><button class="view-button">View</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  
  <style>
    .seller-dashboard {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    
    .dashboard-header {
      background-color: #000;
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .logo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    
    .logo h1 {
      font-size: 1.25rem;
      font-weight: 600;
    }
    
    .logout-button {
      background-color: transparent;
      color: white;
      border: 1px solid white;
      border-radius: 0.25rem;
      padding: 0.5rem 1rem;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .logout-button:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
    
    .dashboard-content {
      display: flex;
      flex: 1;
    }
    
    .sidebar {
      width: 250px;
      background-color: #f3f4f6;
      padding: 1rem 0;
    }
    
    .nav-menu {
      display: flex;
      flex-direction: column;
    }
    
    .nav-item {
      padding: 0.75rem 1.5rem;
      color: #4b5563;
      text-decoration: none;
      transition: background-color 0.2s;
    }
    
    .nav-item:hover {
      background-color: #e5e7eb;
    }
    
    .nav-item.active {
      background-color: #e5e7eb;
      color: #000;
      font-weight: 500;
      border-left: 3px solid #000;
    }
    
    .main-area {
      flex: 1;
      padding: 1.5rem;
    }
    
    h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin-bottom: 1.5rem;
    }
    
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .stat-card {
      background-color: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .stat-card h3 {
      font-size: 0.875rem;
      color: #6b7280;
      margin-bottom: 0.5rem;
    }
    
    .stat-value {
      font-size: 1.5rem;
      font-weight: 600;
    }
    
    .action-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }
    
    .action-card {
      background-color: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .action-card h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }
    
    .action-card p {
      color: #6b7280;
      font-size: 0.875rem;
      margin-bottom: 1rem;
    }
    
    .action-button {
      background-color: #000;
      color: white;
      border: none;
      border-radius: 0.25rem;
      padding: 0.5rem 1rem;
      font-size: 0.875rem;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .action-button:hover {
      background-color: #333;
    }
    
    .recent-section {
      background-color: white;
      border-radius: 0.5rem;
      padding: 1.5rem;
      box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    .recent-section h3 {
      font-size: 1rem;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    
    .data-table th, .data-table td {
      padding: 0.75rem;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    
    .data-table th {
      font-weight: 500;
      color: #6b7280;
    }
    
    .status-delivered {
      color: #10b981;
      background-color: rgba(16, 185, 129, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
    }
    
    .status-processing {
      color: #3b82f6;
      background-color: rgba(59, 130, 246, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
    }
    
    .status-shipped {
      color: #f59e0b;
      background-color: rgba(245, 158, 11, 0.1);
      padding: 0.25rem 0.5rem;
      border-radius: 9999px;
      font-size: 0.75rem;
    }
    
    .view-button {
      background-color: transparent;
      color: #000;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
      cursor: pointer;
    }
    
    .view-button:hover {
      background-color: #f3f4f6;
    }
  </style>
  