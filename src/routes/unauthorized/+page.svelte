<script lang="ts">
    import { goto } from '$app/navigation';
    
    function goToHome() {
      goto('/');
    }
    
    function goToLogin() {
      goto('/login');
    }
  </script>
  
  <svelte:head>
    <title>Unauthorized - QuickBuy</title>
  </svelte:head>
  
  <div class="unauthorized-container">
    <div class="unauthorized-card">
      <div class="logo">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M20 4H4V16H20V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4 8H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 16V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 16V20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4 20H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h1>QuickBuy</h1>
      </div>
      
      <div class="error-icon">
        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M15 9L9 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M9 9L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
      
      <h2>Access Denied</h2>
      <p>You don't have permission to access this page.</p>
      
      <div class="action-buttons">
        <button class="primary-button" on:click={goToHome}>Go to Homepage</button>
        <button class="secondary-button" on:click={goToLogin}>Login with Different Account</button>
      </div>
    </div>
  </div>
  
  <style>
    .unauthorized-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f9fafb;
      padding: 1rem;
    }
    
    .unauthorized-card {
      background-color: #000;
      color: white;
      border-radius: 0.5rem;
      width: 100%;
      max-width: 400px;
      padding: 2rem;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1.5rem;
    }
    
    .logo {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
    }
    
    .logo h1 {
      font-size: 1.5rem;
      font-weight: 700;
    }
    
    .error-icon {
      color: #ef4444;
    }
    
    h2 {
      font-size: 1.5rem;
      font-weight: 600;
      margin: 0;
    }
    
    p {
      color: #d1d5db;
      margin: 0;
    }
    
    .action-buttons {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      width: 100%;
    }
    
    .primary-button {
      background-color: white;
      color: #000;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .primary-button:hover {
      background-color: #f3f4f6;
    }
    
    .secondary-button {
      background-color: transparent;
      color: white;
      border: 1px solid #d1d5db;
      border-radius: 0.25rem;
      padding: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .secondary-button:hover {
      background-color: rgba(255, 255, 255, 0.1);
    }
  </style>
  