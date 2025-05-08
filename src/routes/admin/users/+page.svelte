<script lang="ts">
    import { onMount } from "svelte"
    import { goto } from "$app/navigation"
    import { fetchUsers, updateUserStatus, deleteUser } from "$lib/admin-api"
    import type { User } from "$lib/types"
    import { Search, Filter, Eye, Trash2, Ban, CheckCircle } from "lucide-svelte"
  
    let users: User[] = []
    let totalUsers = 0
    let loading = true
    let error: string | null = null
    let currentPage = 1
    let itemsPerPage = 10
    let searchQuery = ""
    let roleFilter: string = ""
    let statusFilter: string = ""
    let sortBy = "date-desc"
    let showDeleteModal = false
    let userToDelete: User | null = null
    let showStatusModal = false
    let userToUpdate: User | null = null
    let newStatus: "active" | "suspended" | "banned" = "active"
    let actionInProgress = false
    let totalPages: number
    let pageNumbers: number[]
  
    onMount(async () => {
      const url = new URL(window.location.href)
      const role = url.searchParams.get("role")
      const status = url.searchParams.get("status")
      
      if (role) roleFilter = role
      if (status) statusFilter = status
      
      await loadUsers()
    })
  
    async function loadUsers() {
      loading = true
      error = null
      
      try {
        const result = await fetchUsers({
          role: roleFilter || undefined,
          status: statusFilter || undefined,
          search: searchQuery || undefined,
          sortBy,
          page: currentPage,
          limit: itemsPerPage
        })
        
        users = result.users
        totalUsers = result.total
        loading = false
      } catch (err) {
        error = "Failed to load users"
        loading = false
      }
    }
  
    function handleSearch() {
      currentPage = 1
      loadUsers()
    }
  
    function handleFilterChange() {
      currentPage = 1
      loadUsers()
    }
  
    function handleSortChange() {
      loadUsers()
    }
  
    function handlePageChange(page: number) {
      currentPage = page
      loadUsers()
    }
  
    function formatDate(dateString: string): string {
      const date = new Date(dateString)
      return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric"
      }).format(date)
    }
  
    function openDeleteModal(user: User) {
      userToDelete = user
      showDeleteModal = true
    }
  
    function closeDeleteModal() {
      showDeleteModal = false
      userToDelete = null
    }
  
    function openStatusModal(user: User, status: "active" | "suspended" | "banned") {
      userToUpdate = user
      newStatus = status
      showStatusModal = true
    }
  
    function closeStatusModal() {
      showStatusModal = false
      userToUpdate = null
    }
  
    async function handleDeleteUser() {
      if (!userToDelete) return
      
      actionInProgress = true
      
      try {
        await deleteUser(userToDelete.id)
        users = users.filter(u => u.id !== userToDelete?.id)
        totalUsers--
        closeDeleteModal()
      } catch (err) {
        error = "Failed to delete user"
      } finally {
        actionInProgress = false
      }
    }
  
    async function handleUpdateUserStatus() {
      if (!userToUpdate) return
      
      actionInProgress = true
      
      try {
        const updatedUser = await updateUserStatus(userToUpdate.id, newStatus)
        
        // Update the user in the list
        users = users.map(u => 
          u.id === updatedUser.id ? updatedUser : u
        )
        
        closeStatusModal()
      } catch (err) {
        error = "Failed to update user status"
      } finally {
        actionInProgress = false
      }
    }
  
    function viewUserDetails(id: number) {
      goto(`/admin/users/${id}`)
    }
  
    // Calculate total pages
    $: totalPages = Math.ceil(totalUsers / itemsPerPage)
    
    // Generate page numbers for pagination
    $: pageNumbers = Array.from({ length: totalPages }, (_, i) => i + 1)
  </script>
  
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Manage Users</h1>
      <a href="/admin/dashboard" class="text-primary hover:underline">Back to Dashboard</a>
    </div>
  
    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Search users..."
              class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={searchQuery}
              on:keydown={(e) => e.key === 'Enter' && handleSearch()}
            />
            <Search class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
          </div>
        </div>
        
        <div class="flex gap-4">
          <div class="relative">
            <select
              class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary appearance-none"
              bind:value={roleFilter}
              on:change={handleFilterChange}
            >
              <option value="">All Roles</option>
              <option value="customer">Customers</option>
              <option value="seller">Sellers</option>
              <option value="admin">Admins</option>
              <option value="moderator">Moderators</option>
            </select>
            <Filter class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
          </div>
          
          <div class="relative">
            <select
              class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary appearance-none"
              bind:value={statusFilter}
              on:change={handleFilterChange}
            >
              <option value="">All Statuses</option>
              <option value="active">Active</option>
              <option value="suspended">Suspended</option>
              <option value="banned">Banned</option>
            </select>
            <Filter class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
          </div>
          
          <div>
            <select
              class="px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={sortBy}
              on:change={handleSortChange}
            >
              <option value="date-desc">Newest First</option>
              <option value="date-asc">Oldest First</option>
              <option value="name-asc">Name (A-Z)</option>
              <option value="name-desc">Name (Z-A)</option>
            </select>
          </div>
          
          <button
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark"
            on:click={handleSearch}
          >
            Apply
          </button>
        </div>
      </div>
    </div>
  
    {#if error}
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <p>{error}</p>
      </div>
    {/if}
  
    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
      {#if loading}
        <div class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
      {:else if users.length === 0}
        <div class="p-6 text-center">
          <p class="text-gray-500">No users found</p>
        </div>
      {:else}
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              {#each users as user}
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                          {user.fullName.charAt(0).toUpperCase()}
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{user.fullName}</div>
                        <div class="text-sm text-gray-500">{user.email}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {#if user.role === "customer"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Customer
                      </span>
                    {:else if user.role === "seller"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Seller
                      </span>
                    {:else if user.role === "admin"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                        Admin
                      </span>
                    {:else if user.role === "moderator"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                        Moderator
                      </span>
                    {/if}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {#if user.status === "active"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                      </span>
                    {:else if user.status === "suspended"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Suspended
                      </span>
                    {:else if user.status === "banned"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Banned
                      </span>
                    {/if}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {user.createdAt ? formatDate(user.createdAt) : 'N/A'}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button 
                        class="text-primary hover:text-primary-dark"
                        on:click={() => viewUserDetails(user.id)}
                        title="View Details"
                      >
                        <Eye class="h-5 w-5" />
                      </button>
                      
                      {#if user.status !== "active"}
                        <button 
                          class="text-green-600 hover:text-green-800"
                          on:click={() => openStatusModal(user, "active")}
                          title="Activate User"
                        >
                          <CheckCircle class="h-5 w-5" />
                        </button>
                      {/if}
                      
                      {#if user.status !== "banned"}
                        <button 
                          class="text-red-600 hover:text-red-800"
                          on:click={() => openStatusModal(user, "banned")}
                          title="Ban User"
                        >
                          <Ban class="h-5 w-5" />
                        </button>
                      {/if}
                      
                      <button 
                        class="text-red-600 hover:text-red-800"
                        on:click={() => openDeleteModal(user)}
                        title="Delete User"
                      >
                        <Trash2 class="h-5 w-5" />
                      </button>
                    </div>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        </div>
      {/if}
    </div>
  
    <!-- Pagination -->
    {#if totalPages > 1}
      <div class="flex justify-center mt-6">
        <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
          <button
            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            disabled={currentPage === 1}
            on:click={() => handlePageChange(currentPage - 1)}
          >
            <span class="sr-only">Previous</span>
            &laquo;
          </button>
          
          {#each pageNumbers as page}
            <button
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium {currentPage === page ? 'text-primary bg-primary-50 z-10' : 'text-gray-500 hover:bg-gray-50'}"
              on:click={() => handlePageChange(page)}
            >
              {page}
            </button>
          {/each}
          
          <button
            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
            disabled={currentPage === totalPages}
            on:click={() => handlePageChange(currentPage + 1)}
          >
            <span class="sr-only">Next</span>
            &raquo;
          </button>
        </nav>
      </div>
    {/if}
  
    <!-- Delete Modal -->
    {#if showDeleteModal}
      <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium mb-4">Delete User</h3>
          <p class="mb-4">Are you sure you want to delete <strong>{userToDelete?.fullName}</strong>? This action cannot be undone.</p>
          <div class="flex justify-end space-x-3">
            <button 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              on:click={closeDeleteModal}
              disabled={actionInProgress}
            >
              Cancel
            </button>
            <button 
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              on:click={handleDeleteUser}
              disabled={actionInProgress}
            >
              {actionInProgress ? 'Deleting...' : 'Delete'}
            </button>
          </div>
        </div>
      </div>
    {/if}
  
    <!-- Status Update Modal -->
    {#if showStatusModal}
      <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium mb-4">
            {newStatus === "active" ? "Activate" : newStatus === "suspended" ? "Suspend" : "Ban"} User
          </h3>
          <p class="mb-4">
            {#if newStatus === "active"}
              Are you sure you want to activate <strong>{userToUpdate?.fullName}</strong>? This will restore their access to the platform.
            {:else if newStatus === "suspended"}
              Are you sure you want to suspend <strong>{userToUpdate?.fullName}</strong>? This will temporarily restrict their access to the platform.
            {:else}
              Are you sure you want to ban <strong>{userToUpdate?.fullName}</strong>? This will permanently restrict their access to the platform.
            {/if}
          </p>
          <div class="flex justify-end space-x-3">
            <button 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              on:click={closeStatusModal}
              disabled={actionInProgress}
            >
              Cancel
            </button>
            <button 
              class="px-4 py-2 {newStatus === 'active' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-md"
              on:click={handleUpdateUserStatus}
              disabled={actionInProgress}
            >
              {actionInProgress ? 'Updating...' : newStatus === "active" ? "Activate" : newStatus === "suspended" ? "Suspend" : "Ban"}
            </button>
          </div>
        </div>
      </div>
    {/if}
  </div>
  