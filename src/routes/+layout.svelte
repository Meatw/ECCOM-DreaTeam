<script lang="ts">
  import "../app.css";
  import { onMount } from 'svelte';
  import { isAuthenticated, getCurrentUser, logout } from '$lib/auth';
  
  let user = null;
  let isLoggedIn = false;
  
  onMount(() => {
    isLoggedIn = isAuthenticated();
    if (isLoggedIn) {
      user = getCurrentUser();
    }
  });
  
  function handleLogout() {
    logout();
    isLoggedIn = false;
    user = null;
  }
</script>

<!-- Add a user menu in the header if needed -->
{#if isLoggedIn && user}
  <div class="user-menu">
    <span>Welcome, {user.fullName}</span>
    <button on:click={handleLogout}>Logout</button>
  </div>
{/if}

<slot />

<style>
  .user-menu {
    position: fixed;
    top: 0;
    right: 0;
    background-color: #000;
    color: white;
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    z-index: 100;
  }
  
  .user-menu button {
    background-color: white;
    color: #000;
    border: none;
    border-radius: 0.25rem;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    cursor: pointer;
  }
</style>
