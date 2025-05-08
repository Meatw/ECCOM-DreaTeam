<script lang="ts">
  import { onMount } from "svelte"
  import { goto } from "$app/navigation"
  import { fetchAdminStats } from "$lib/admin-api"
  import type { AdminStats } from "$lib/types"
  import { BarChart, LineChart, PieChart } from "lucide-svelte"

  let stats: AdminStats | null = null
  let loading = true
  let error: string | null = null

  onMount(async () => {
    try {
      stats = await fetchAdminStats()
      loading = false
    } catch (err) {
      error = "Failed to load admin statistics"
      loading = false
    }
  })

  function formatCurrency(amount: number): string {
    return new Intl.NumberFormat("en-US", {
      style: "currency",
      currency: "USD",
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount)
  }

  function formatNumber(num: number): string {
    return new Intl.NumberFormat("en-US").format(num)
  }
</script>

<div class="container mx-auto px-4 py-8">
  <h1 class="text-3xl font-bold mb-8">Admin Dashboard</h1>

  {#if loading}
    <div class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
    </div>
  {:else if error}
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <p>{error}</p>
    </div>
  {:else if stats}
    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-amber-50 p-6 rounded-lg shadow-sm border border-amber-200">
        <h3 class="text-lg font-semibold mb-2">Pending Approvals</h3>
        <p class="text-3xl font-bold text-amber-600">{stats.pendingBusinessApprovals}</p>
        <p class="text-sm text-gray-600 mt-2">Businesses waiting for approval</p>
        <button 
          class="mt-4 px-4 py-2 bg-amber-600 text-white rounded hover:bg-amber-700 transition-colors"
          on:click={() => goto('/admin/businesses?status=pending')}
        >
          Review Businesses
        </button>
      </div>

      <div class="bg-red-50 p-6 rounded-lg shadow-sm border border-red-200">
        <h3 class="text-lg font-semibold mb-2">Pending Reports</h3>
        <p class="text-3xl font-bold text-red-600">{stats.pendingReports}</p>
        <p class="text-sm text-gray-600 mt-2">Reports waiting for review</p>
        <button 
          class="mt-4 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition-colors"
          on:click={() => goto('/admin/reports?status=pending')}
        >
          Review Reports
        </button>
      </div>

      <div class="bg-green-50 p-6 rounded-lg shadow-sm border border-green-200">
        <h3 class="text-lg font-semibold mb-2">Monthly Revenue</h3>
        <p class="text-3xl font-bold text-green-600">{formatCurrency(stats.revenueThisMonth)}</p>
        <p class="text-sm text-gray-600 mt-2">Revenue this month</p>
        <button 
          class="mt-4 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
          on:click={() => goto('/admin/reports/revenue')}
        >
          View Reports
        </button>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Total Users</h3>
        <p class="text-2xl font-bold">{formatNumber(stats.totalUsers)}</p>
        <div class="flex items-center mt-2">
          <span class="text-xs text-gray-500">
            +{formatNumber(stats.newUsersThisMonth)} this month
          </span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Total Products</h3>
        <p class="text-2xl font-bold">{formatNumber(stats.totalProducts)}</p>
        <div class="flex items-center mt-2">
          <span class="text-xs text-gray-500">
            From {formatNumber(stats.totalSellers)} sellers
          </span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Total Orders</h3>
        <p class="text-2xl font-bold">{formatNumber(stats.totalOrders)}</p>
        <div class="flex items-center mt-2">
          <span class="text-xs text-gray-500">
            +{formatNumber(stats.newOrdersThisMonth)} this month
          </span>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <h3 class="text-sm font-medium text-gray-500 mb-2">Total Revenue</h3>
        <p class="text-2xl font-bold">{formatCurrency(stats.totalRevenue)}</p>
        <div class="flex items-center mt-2">
          <span class="text-xs text-gray-500">
            Lifetime platform revenue
          </span>
        </div>
      </div>
    </div>

    <!-- User Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-1">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">User Distribution</h3>
          <PieChart class="h-5 w-5 text-gray-400" />
        </div>
        <div class="space-y-4">
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium">Customers</span>
              <span class="text-sm font-medium">{Math.round(stats.totalCustomers / stats.totalUsers * 100)}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: {stats.totalCustomers / stats.totalUsers * 100}%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium">Sellers</span>
              <span class="text-sm font-medium">{Math.round(stats.totalSellers / stats.totalUsers * 100)}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-600 h-2 rounded-full" style="width: {stats.totalSellers / stats.totalUsers * 100}%"></div>
            </div>
          </div>
          <div>
            <div class="flex justify-between mb-1">
              <span class="text-sm font-medium">Admins</span>
              <span class="text-sm font-medium">{Math.round(stats.totalAdmins / stats.totalUsers * 100)}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="bg-purple-600 h-2 rounded-full" style="width: {stats.totalAdmins / stats.totalUsers * 100}%"></div>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 col-span-2">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">Platform Activity</h3>
          <LineChart class="h-5 w-5 text-gray-400" />
        </div>
        <div class="h-64 flex items-center justify-center">
          <p class="text-gray-500">Chart visualization would go here</p>
          <!-- In a real app, you would use a chart library like Chart.js or D3.js -->
        </div>
      </div>
    </div>

    <!-- Admin Actions -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
      <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="/admin/users" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg transition-colors">
          <h4 class="font-medium">Manage Users</h4>
          <p class="text-sm text-gray-600 mt-1">View and manage user accounts</p>
        </a>
        <a href="/admin/businesses" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg transition-colors">
          <h4 class="font-medium">Manage Businesses</h4>
          <p class="text-sm text-gray-600 mt-1">Approve and manage business accounts</p>
        </a>
        <a href="/admin/reports" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg transition-colors">
          <h4 class="font-medium">Review Reports</h4>
          <p class="text-sm text-gray-600 mt-1">Handle reported content</p>
        </a>
        <a href="/admin/settings" class="bg-gray-100 hover:bg-gray-200 p-4 rounded-lg transition-colors">
          <h4 class="font-medium">Admin Settings</h4>
          <p class="text-sm text-gray-600 mt-1">Manage admin accounts and permissions</p>
        </a>
      </div>
    </div>
  {/if}
</div>
