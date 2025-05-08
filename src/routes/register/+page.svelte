<script lang="ts">
    let fullName = '';
    let email = '';
    let password = '';
    let confirmPassword = '';
    let accountType = 'customer';
    let storeName = '';
    let storeDescription = '';
    let phoneNumber = '';
    let isLoading = false;
    let errorMessage = '';
    
    $: isSellerAccount = accountType === 'seller';
    
    function goToLogin() {
      window.location.href = '/login';
    }
    
    async function handleRegister(event: SubmitEvent) {
      event.preventDefault();
      
      // Validation
      if (!fullName || !email || !password || !confirmPassword) {
        errorMessage = 'Please fill in all required fields';
        return;
      }
      
      if (password !== confirmPassword) {
        errorMessage = 'Passwords do not match';
        return;
      }
      
      if (isSellerAccount && (!storeName || !storeDescription || !phoneNumber)) {
        errorMessage = 'Please fill in all seller information';
        return;
      }
      
      isLoading = true;
      errorMessage = '';
      
      try {
        const userData = {
          fullName,
          email,
          password,
          accountType
        };
        
        if (isSellerAccount) {
          Object.assign(userData, {
            storeName,
            storeDescription,
            phoneNumber
          });
        }
        
        // In a real app, this would call your PHP backend
        // const response = await fetch('/api/auth/register', {
        //   method: 'POST',
        //   headers: {
        //     'Content-Type': 'application/json'
        //   },
        //   body: JSON.stringify(userData)
        // });
        
        // const data = await response.json();
        
        // if (!response.ok) {
        //   throw new Error(data.message || 'Registration failed');
        // }
        
        // For demo purposes, simulate a successful registration
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        // Registration successful, redirect to login
        window.location.href = '/login';
      } catch (error) {
        console.error('Registration error:', error);
        errorMessage = error instanceof Error ? error.message : 'Registration failed. Please try again.';
      } finally {
        isLoading = false;
      }
    }
  </script>
  
  <svelte:head>
    <title>Register - QuickBuy</title>
  </svelte:head>
  
  <div class="register-container">
    <div class="register-card">
      <div class="register-header">
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
        <button class="tab-button" on:click={goToLogin}>Login</button>
        <button class="tab-button active">Register</button>
      </div>
      
      <form on:submit={handleRegister} class="register-form">
        {#if errorMessage}
          <div class="error-message">
            {errorMessage}
          </div>
        {/if}
        
        <div class="account-type">
          <label>Account Type</label>
          <div class="account-options">
            <label class="account-option">
              <input 
                type="radio" 
                name="accountType" 
                value="customer" 
                bind:group={accountType}
              />
              <span>Register as Customer</span>
            </label>
            <label class="account-option">
              <input 
                type="radio" 
                name="accountType" 
                value="seller" 
                bind:group={accountType}
              />
              <span>Register as Seller</span>
            </label>
          </div>
        </div>
        
        <div class="form-group">
          <label for="fullName">Full Name</label>
          <input 
            type="text" 
            id="fullName" 
            bind:value={fullName} 
            placeholder="John Doe" 
            required
          />
        </div>
        
        <div class="form-group">
          <label for="email">Email</label>
          <input 
            type="email" 
            id="email" 
            bind:value={email} 
            placeholder="name@example.com" 
            required
          />
        </div>
        
        {#if isSellerAccount}
          <div class="form-group">
            <label for="storeName">Store Name</label>
            <input 
              type="text" 
              id="storeName" 
              bind:value={storeName} 
              placeholder="Your store name" 
              required={isSellerAccount}
            />
          </div>
          
          <div class="form-group">
            <label for="storeDescription">Store Description</label>
            <textarea 
              id="storeDescription" 
              bind:value={storeDescription} 
              placeholder="Brief description of your store" 
              required={isSellerAccount}
            ></textarea>
          </div>
          
          <div class="form-group">
            <label for="phoneNumber">Phone Number</label>
            <input 
              type="tel" 
              id="phoneNumber" 
              bind:value={phoneNumber} 
              placeholder="(123) 456-7890" 
              required={isSellerAccount}
            />
          </div>
        {/if}
        
        <div class="form-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            bind:value={password} 
            required
          />
        </div>
        
        <div class="form-group">
          <label for="confirmPassword">Confirm Password</label>
          <input 
            type="password" 
            id="confirmPassword" 
            bind:value={confirmPassword} 
            required
          />
        </div>
        
        <button type="submit" class="register-button" disabled={isLoading}>
          {isLoading ? 'Registering...' : 'Register'}
        </button>
        
        <div class="login-link">
          Already have an account? <a href="/login" on:click|preventDefault={goToLogin}>Login</a>
        </div>
        
        <div class="terms-notice">
          By continuing, you agree to QuickBuy's Terms of Service and Privacy Policy
        </div>
      </form>
    </div>
  </div>
  
  <style>
    .register-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background-color: #f9fafb;
      padding: 1rem;
    }
    
    .register-card {
      background-color: #000;
      color: white;
      border-radius: 0.5rem;
      width: 100%;
      max-width: 400px;
      padding: 2rem;
    }
    
    .register-header {
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
    
    .register-form {
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
    
    .account-type {
      margin-bottom: 0.5rem;
    }
    
    .account-options {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      margin-top: 0.5rem;
    }
    
    .account-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
    }
    
    .form-group {
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }
    
    label {
      font-size: 0.875rem;
    }
    
    input, textarea {
      padding: 0.75rem;
      border-radius: 0.25rem;
      border: none;
      background-color: white;
      color: #000;
    }
    
    textarea {
      min-height: 80px;
      resize: vertical;
    }
    
    .register-button {
      background-color: white;
      color: #000;
      border: none;
      border-radius: 0.25rem;
      padding: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.2s;
    }
    
    .register-button:hover {
      background-color: #f3f4f6;
    }
    
    .register-button:disabled {
      background-color: #d1d5db;
      cursor: not-allowed;
    }
    
    .login-link {
      text-align: center;
      font-size: 0.875rem;
    }
    
    .login-link a {
      color: white;
      text-decoration: underline;
    }
    
    .terms-notice {
      font-size: 0.75rem;
      color: #9ca3af;
      text-align: center;
    }
  </style>
  