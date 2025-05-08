<script lang="ts">
    import { onMount } from "svelte"
    import { fetchUsers, createAdminUser, updateAdminPermissions } from "$lib/admin-api"
    import type { User, AdminPermission } from "$lib/types"
    import { Search, Plus, Save, X } from "lucide-svelte"
  
    let adminUsers: User[] = []
    let loading = true
    let error: string | null = null
    let searchQuery = ""
    let showCreateModal = false
    let showEditModal = false
    let userToEdit: User | null = null
    let actionInProgress = false
  
    // Form data for creating a new admin
    let newAdminData = {
      fullName: "",
      email: "",
      password: "",
      confirmPassword: "",
      role: "moderator" as "admin" | "moderator",
      permissions: [] as AdminPermission[]
    }
  
    // Form data for editing permissions
    let editPermissions: AdminPermission[] = []
  
    // Available permissions
    const availablePermissions: { value: AdminPermission, label: string }[] = [
      { value: "users", label: "User Management" },
      { value: "businesses", label: "Business Management" },
      { value: "content", label: "Content Management" },
      { value: "reports", label: "Report Management" }
    ]
  
    onMount(async () => {
      await loadAdminUsers()
    })
  
    async function loadAdminUsers() {
      loading = true
      error = null
      
      try {
        // Fetch admin and moderator users
        const adminResult = await fetchUsers({
          role: "admin",
          search: searchQuery || undefined
        })
        
        const moderatorResult = await fetchUsers({
          role: "moderator",
          search: searchQuery || undefined
        })
        
        adminUsers = [...adminResult.users, ...moderatorResult.users]
        loading = false
      } catch (err) {
        error = "Failed to load admin users"
        loading = false
      }
    }
  
    function handleSearch() {
      loadAdminUsers()
    }
  
    function openCreateModal() {
      // Reset form data
      newAdminData = {
        fullName: "",
        email: "",
        password: "",
        confirmPassword: "",
        role: "moderator",
        permissions: []
      }
      
      showCreateModal = true
    }
  
    function closeCreateModal() {
      showCreateModal = false
    }
  
    function openEditModal(user: User) {
      userToEdit = user
      editPermissions = [...(user.permissions || [])]
      showEditModal = true
    }
  
    function closeEditModal() {
      showEditModal = false
      userToEdit = null
      editPermissions = []
    }
  
    async function handleCreateAdmin() {
      // Validate form
      if (!newAdminData.fullName.trim()) {
        error = "Full name is required"
        return
      }
      
      if (!newAdminData.email.trim() || !newAdminData.email.includes('@')) {
        error = "Valid email is required"
        return
      }
      
      if (!newAdminData.password.trim() || newAdminData.password.length < 8) {
        error = "Password must be at least 8 characters"
        return
      }
      
      if (newAdminData.password !== newAdminData.confirmPassword) {
        error = "Passwords do not match"
        return
      }
      
      if (newAdminData.permissions.length === 0) {
        error = "At least one permission must be selected"
        return
      }
      
      actionInProgress = true
      error = null
      
      try {
        const newAdmin = await createAdminUser({
          fullName: newAdminData.fullName,
          email: newAdminData.email,
          password: newAdminData.password,
          role: newAdminData.role,
          permissions: newAdminData.permissions
        })
        
        // Add the new admin to the list
        adminUsers = [newAdmin, ...adminUsers]
        
        closeCreateModal()
      } catch (err) {
        error = "Failed to create admin user"
      } finally {
        actionInProgress = false
      }
    }
  
    async function handleUpdatePermissions() {
      if (!userToEdit) return
      
      if (editPermissions.length === 0) {
        error = "At least one permission must be selected"
        return
      }
      
      actionInProgress = true
      error = null
      
      try {
        const updatedUser = await updateAdminPermissions(userToEdit.id, editPermissions)
        
        // Update the user in the list
        adminUsers = adminUsers.map(u => 
          u.id === updatedUser.id ? updatedUser : u
        )
        
        closeEditModal()
      } catch (err) {
        error = "Failed to update permissions"
      } finally {
        actionInProgress = false
      }
    }
  
    function formatDate(dateString: string): string {
      const date = new Date(dateString)
      return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric"
      }).format(date)
    }
  
    function togglePermission(permission: AdminPermission) {
      if (editPermissions.includes(permission)) {
        editPermissions = editPermissions.filter(p => p !== permission)
      } else {
        editPermissions = [...editPermissions, permission]
      }
    }
  
    function toggleNewPermission(permission: AdminPermission) {
      if (newAdminData.permissions.includes(permission)) {
        newAdminData.permissions = newAdminData.permissions.filter(p => p !== permission)
      } else {
        newAdminData.permissions = [...newAdminData.permissions, permission]
      }
    }
  </script>
  
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Admin Settings</h1>
      <a href="/admin/dashboard" class="text-primary hover:underline">Back to Dashboard</a>
    </div>
  
    <!-- Admin Management Section -->
    <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 mb-8">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-semibold">Admin Users</h2>
        <button
          class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark flex items-center gap-2"
          on:click={openCreateModal}
        >
          <Plus class="h-5 w-5" />
          Create Admin
        </button>
      </div>
  
      <!-- Search -->
      <div class="mb-6">
        <div class="relative max-w-md">
          <input
            type="text"
            placeholder="Search admins..."
            class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
            bind:value={searchQuery}
            on:keydown={(e) => e.key === 'Enter' && handleSearch()}
          />
          <Search class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
        </div>
      </div>
  
      {#if error}
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
          <p>{error}</p>
        </div>
      {/if}
  
      <!-- Admin Users Table -->
      <div class="overflow-x-auto">
        {#if loading}
          <div class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
          </div>
        {:else if adminUsers.length === 0}
          <div class="p-6 text-center">
            <p class="text-gray-500">No admin users found</p>
          </div>
        {:else}
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Permissions</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              {#each adminUsers as user}
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{user.fullName}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-500">{user.email}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {#if user.role === "admin"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                        Admin
                      </span>
                    {:else if user.role === "moderator"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                        Moderator
                      </span>
                    {/if}
                  </td>
                  <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-1">
                      {#if user.permissions && user.permissions.length > 0}
                        {#each user.permissions as permission}
                          <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                            {permission === "users" ? "Users" : 
                             permission === "businesses" ? "Businesses" : 
                             permission === "content" ? "Content" : 
                             permission === "reports" ? "Reports" : 
                             permission}
                          </span>
                        {/each}
                      {:else}
                        <span class="text-sm text-gray-500">No permissions</span>
                      {/if}
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {user.createdAt ? formatDate(user.createdAt) : 'N/A'}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button
                      class="text-primary hover:text-primary-dark"
                      on:click={() => openEditModal(user)}
                    >
                      Edit Permissions
                    </button>
                  </td>
                </tr>
              {/each}
            </tbody>
          </table>
        {/if}
      </div>
    </div>
  </div>
  
  <!-- Create Admin Modal -->
  {#if showCreateModal}
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium">Create Admin User</h3>
          <button 
            class="text-gray-400 hover:text-gray-500"
            on:click={closeCreateModal}
            disabled={actionInProgress}
          >
            <X class="h-5 w-5" />
          </button>
        </div>
        
        {#if error}
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p>{error}</p>
          </div>
        {/if}
        
        <div class="space-y-4">
          <div>
            <label for="fullName" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
              type="text"
              id="fullName"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={newAdminData.fullName}
            />
          </div>
          
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              type="email"
              id="email"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={newAdminData.email}
            />
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input
              type="password"
              id="password"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={newAdminData.password}
            />
          </div>
          
          <div>
            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
            <input
              type="password"
              id="confirmPassword"
              class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-primary"
              bind:value={newAdminData.confirmPassword}
            />
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <div class="flex gap-4">
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio text-primary focus:ring-primary"
                  bind:group={newAdminData.role}
                  value="moderator"
                />
                <span class="ml-2">Moderator</span>
              </label>
              <label class="inline-flex items-center">
                <input
                  type="radio"
                  class="form-radio text-primary focus:ring-primary"
                  bind:group={newAdminData.role}
                  value="admin"
                />
                <span class="ml-2">Admin</span>
              </label>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Permissions</label>
            <div class="space-y-2">
              {#each availablePermissions as permission}
                <label class="inline-flex items-center">
                  <input
                    type="checkbox"
                    class="form-checkbox text-primary focus:ring-primary"
                    checked={newAdminData.permissions.includes(permission.value)}
                    on:change={() => toggleNewPermission(permission.value)}
                  />
                  <span class="ml-2">{permission.label}</span>
                </label>
              {/each}
            </div>
          </div>
        </div>
        
        <div class="flex justify-end mt-6">
          <button 
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 mr-2"
            on:click={closeCreateModal}
            disabled={actionInProgress}
          >
            Cancel
          </button>
          <button 
            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center gap-2"
            on:click={handleCreateAdmin}
            disabled={actionInProgress}
          >
            {#if actionInProgress}
              <div class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white"></div>
              Creating...
            {:else}
              <Plus class="h-4 w-4" />
              Create
            {/if}
          </button>
        </div>
      </div>
    </div>
  {/if}
  
  <!-- Edit Permissions Modal -->
  {#if showEditModal && userToEdit}
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium">Edit Permissions: {userToEdit.fullName}</h3>
          <button 
            class="text-gray-400 hover:text-gray-500"
            on:click={closeEditModal}
            disabled={actionInProgress}
          >
            <X class="h-5 w-5" />
          </button>
        </div>
        
        {#if error}
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <p>{error}</p>
          </div>
        {/if}
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
          <div class="space-y-3">
            {#each availablePermissions as permission}
              <label class="inline-flex items-center">
                <input
                  type="checkbox"
                  class="form-checkbox text-primary focus:ring-primary h-5 w-5"
                  checked={editPermissions.includes(permission.value)}
                  on:change={() => togglePermission(permission.value)}
                />
                <span class="ml-2">{permission.label}</span>
              </label>
            {/each}
          </div>
        </div>
        
        <div class="flex justify-end mt-6">
          <button 
            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 mr-2"
            on:click={closeEditModal}
            disabled={actionInProgress}
          >
            Cancel
          </button>
          <button 
            class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark flex items-center gap-2"
            on:click={handleUpdatePermissions}
            disabled={actionInProgress}
          >
            {#if actionInProgress}
              <div class="animate-spin rounded-full h-4 w-4 border-t-2 border-b-2 border-white"></div>
              Saving...
            {:else}
              <Save class="h-4 w-4" />
              Save
            {/if}
          </button>
        </div>
      </div>
    </div>
  {/if}
  