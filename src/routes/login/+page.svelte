<script lang="ts">
  import { onMount } from 'svelte';
  import { goto } from '$app/navigation';
  import { login, isAuthenticated, getCurrentUser } from '$lib/auth';
  
  let email = '';
  let password = '';
  let isLoading = false;
  let errorMessage = '';
  let rememberMe = false;
  
  onMount(() => {
    // Redirect if already logged in
    if (isAuthenticated()) {
      const user = getCurrentUser();
      if (user) {
        // Redirect based on user role
        if (user.role === 'admin') {
          goto('/admin/dashboard');
        } else if (user.role === 'seller') {
          goto('/seller/dashboard');
        } else {
          goto('/');
        }
      }
    }
  });
  
  async function handleLogin(event: SubmitEvent) {
    event.preventDefault();
    
    if (!email || !password) {
      errorMessage = 'Please enter both email and password';
      return;
    }
    
    isLoading = true;
    errorMessage = '';
    
    try {
      // For demo purposes, simulate different user roles based on email
      // In a real app, this would be handled by your backend
      let mockUser;
      
      if (email.includes('admin')) {
        mockUser = {
          id: 1,
          fullName: 'Admin User',
          email: email,
          role: 'admin' as const,
          token: 'mock-admin-token'
        };
        goto('/admin/dashboard');
      } else if (email.includes('seller')) {
        mockUser = {
          id: 2,
          fullName: 'Seller User',
          email: email,
          role: 'seller' as const,
          token: 'mock-seller-token'
        };
        goto('/seller/dashboard');
      } else {
        mockUser = {
          id: 3,
          fullName: 'Customer User',
          email: email,
          role: 'customer' as const,
          token: 'mock-customer-token'
        };
        goto('/');
      }
      
      // In a real app, this would call the login function
      // const user = await login(email, password);
      
      // For demo, we'll use our mock user
      localStorage.setItem('quickbuy_user', JSON.stringify(mockUser));
      localStorage.setItem('quickbuy_token', mockUser.token);
      
    } catch (error) {
      console.error('Login error:', error);
      errorMessage = error instanceof Error ? error.message : 'Login failed. Please try again.';
    } finally {
      isLoading = false;
    }
  }
  
  function goToRegister() {
    goto('/register');
  }
</script>

<svelte:head>
  <title>Login - QuickBuy</title>
</svelte:head>

<div class="login-container">
  <div class="login-card">
    <div class="login-header">
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
      <p class="tagline">Your One-Stop for All you need</p>
    </div>
    
    <div class="tab-navigation">
      <button class="tab-button active">Login</button>
      <button class="tab-button" on:click={goToRegister}>Register</button>
    </div>
    
    <form on:submit={handleLogin} class="login-form">
      {#if errorMessage}
        <div class="error-message">
          {errorMessage}
        </div>
      {/if}
      
      <div class="form-group">
        <label for="email">Email</label>
        <input 
          type="email" 
          id="email" 
          bind:value={email} 
          placeholder="name@example.com" 
          required
        />
        <p class="form-hint">
          Demo: Use "admin@example.com", "seller@example.com", or any other email
        </p>
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <div class="password-input">
          <input 
            type="password" 
            id="password" 
            bind:value={password} 
            required
          />
        </div>
        <p class="form-hint">
          Demo: Any password will work
        </p>
      </div>
      
      <div class="form-options">
        <label class="remember-me">
          <input type="checkbox" bind:checked={rememberMe} />
          <span>Remember me</span>
        </label>
        <a href="/forgot-password" class="forgot-password">Forgot password?</a>
      </div>
      
      <button type="submit" class="login-button" disabled={isLoading}>
        {isLoading ? 'Logging in...' : 'Login'}
      </button>
      
      <div class="register-link">
        Don't have an account? <a href="/register" on:click|preventDefault={goToRegister}>Register</a>
      </div>
      
      <div class="terms-notice">
        By continuing, you agree to QuickBuy's Terms of Service and Privacy Policy
      </div>
    </form>
  </div>
</div>

<style>
  .login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background-color: #f9fafb;
    padding: 1rem;
  }
  
  .login-card {
    background-color: #000;
    color: white;
    border-radius: 0.5rem;
    width: 100%;
    max-width: 400px;
    padding: 2rem;
  }
  
  .login-header {
    text-align: center;
    margin-bottom: 1.5rem;
  }
  
  .logo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
  }
  
  .logo h1 {
    font-size: 1.5rem;
    font-weight: 700;
  }
  
  .tagline {
    font-size: 0.875rem;
    color: #d1d5db;
  }
  
  .tab-navigation {
    display: flex;
    margin-bottom: 1.5rem;
    border-bottom: 1px solid #333;
  }
  
  .tab-button {
    flex: 1;
    background: none;
    border: none;
    color: #d1d5db;
    padding: 0.75rem;
    font-size: 0.875rem;
    cursor: pointer;
    transition: color 0.2s;
  }
  
  .tab-button.active {
    color: white;
    border-bottom: 2px solid white;
  }
  
  .login-form {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
  }
  
  .error-message {
    background-color: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    padding: 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
  }
  
  .form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  label {
    font-size: 0.875rem;
  }
  
  input {
    padding: 0.75rem;
    border-radius: 0.25rem;
    border: none;
    background-color: white;
    color: #000;
  }
  
  .password-input {
    position: relative;
  }
  
  .form-hint {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
  }
  
  .form-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
  }
  
  .remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
  }
  
  .forgot-password {
    color: #d1d5db;
    text-decoration: none;
  }
  
  .forgot-password:hover {
    text-decoration: underline;
  }
  
  .login-button {
    background-color: white;
    color: #000;
    border: none;
    border-radius: 0.25rem;
    padding: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
  }
  
  .login-button:hover {
    background-color: #f3f4f6;
  }
  
  .login-button:disabled {
    background-color: #d1d5db;
    cursor: not-allowed;
  }
  
  .register-link {
    text-align: center;
    font-size: 0.875rem;
  }
  
  .register-link a {
    color: white;
    text-decoration: underline;
  }
  
  .terms-notice {
    font-size: 0.75rem;
    color: #9ca3af;
    text-align: center;
  }
</style>
