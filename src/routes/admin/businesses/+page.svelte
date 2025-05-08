<script lang="ts">
    import { onMount } from "svelte"
    import { goto } from "$app/navigation"
    import { fetchBusinesses, updateBusinessStatus, deleteBusiness } from "$lib/admin-api"
    import type { Business, BusinessStatus } from "$lib/types"
    import { Search, Filter, Check, X, Trash2, Eye } from "lucide-svelte"
  
    let businesses: Business[] = []
    let totalBusinesses = 0
    let loading = true
    let error: string | null = null
    let currentPage = 1
    let itemsPerPage = 10
    let searchQuery = ""
    let statusFilter: BusinessStatus | "" = ""
    let sortBy = "date-desc"
    let showDeleteModal = false
    let businessToDelete: Business | null = null
    let showApproveModal = false
    let showRejectModal = false
    let businessToUpdate: Business | null = null
    let rejectionReason = ""
    let actionInProgress = false
  
    // URL params handling
    onMount(async () => {
      const url = new URL(window.location.href)
      const status = url.searchParams.get("status")
      if (status) {
        statusFilter = status as BusinessStatus
      }
      
      await loadBusinesses()
    })
  
    async function loadBusinesses() {
      loading = true
      error = null
      
      try {
        const result = await fetchBusinesses({
          status: statusFilter || undefined,
          search: searchQuery || undefined,
          sortBy,
          page: currentPage,
          limit: itemsPerPage
        })
        
        businesses = result.businesses
        totalBusinesses = result.total
        loading = false
      } catch (err) {
        error = "Failed to load businesses"
        loading = false
      }
    }
  
    function handleSearch() {
      currentPage = 1
      loadBusinesses()
    }
  
    function handleFilterChange() {
      currentPage = 1
      loadBusinesses()
    }
  
    function handleSortChange() {
      loadBusinesses()
    }
  
    function handlePageChange(page: number) {
      currentPage = page
      loadBusinesses()
    }
  
    function formatDate(dateString: string): string {
      const date = new Date(dateString)
      return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric"
      }).format(date)
    }
  
    function openDeleteModal(business: Business) {
      businessToDelete = business
      showDeleteModal = true
    }
  
    function closeDeleteModal() {
      showDeleteModal = false
      businessToDelete = null
    }
  
    function openApproveModal(business: Business) {
      businessToUpdate = business
      showApproveModal = true
    }
  
    function closeApproveModal() {
      showApproveModal = false
      businessToUpdate = null
    }
  
    function openRejectModal(business: Business) {
      businessToUpdate = business
      showRejectModal = true
      rejectionReason = ""
    }
  
    function closeRejectModal() {
      showRejectModal = false
      businessToUpdate = null
      rejectionReason = ""
    }
  
    async function handleDeleteBusiness() {
      if (!businessToDelete) return
      
      actionInProgress = true
      
      try {
        await deleteBusiness(businessToDelete.id)
        businesses = businesses.filter(b => b.id !== businessToDelete?.id)
        totalBusinesses--
        closeDeleteModal()
      } catch (err) {
        error = "Failed to delete business"
      } finally {
        actionInProgress = false
      }
    }
  
    async function handleApproveBusiness() {
      if (!businessToUpdate) return
      
      actionInProgress = true
      
      try {
        const updatedBusiness = await updateBusinessStatus(businessToUpdate.id, "approved")
        
        // Update the business in the list
        businesses = businesses.map(b => 
          b.id === updatedBusiness.id ? updatedBusiness : b
        )
        
        closeApproveModal()
      } catch (err) {
        error = "Failed to approve business"
      } finally {
        actionInProgress = false
      }
    }
  
    async function handleRejectBusiness() {
      if (!businessToUpdate) return
      
      actionInProgress = true
      
      try {
        const updatedBusiness = await updateBusinessStatus(
          businessToUpdate.id, 
          "rejected", 
          rejectionReason
        )
        
        // Update the business in the list
        businesses = businesses.map(b => 
          b.id === updatedBusiness.id ? updatedBusiness : b
        )
        
        closeRejectModal()
      } catch (err) {
        error = "Failed to reject business"
      } finally {
        actionInProgress = false
      }
    }
  
    function viewBusinessDetails(id: number) {
      goto(`/admin/businesses/${id}`)
    }
  
    // Calculate total pages
    $: totalPages = Math.ceil(totalBusinesses / itemsPerPage)
    
    // Generate page numbers for pagination
    $: pageNumbers = Array.from({ length: totalPages }, (_, i) => i + 1)
  </script>
  
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Manage Businesses</h1>
      <a href="/admin/dashboard" class="text-primary hover:underline">Back to Dashboard</a>
    </div>
  
    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Search businesses..."
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
              bind:value={statusFilter}
              on:change={handleFilterChange}
            >
              <option value="">All Statuses</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="rejected">Rejected</option>
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
  
    <!-- Businesses Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
      {#if loading}
        <div class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
      {:else if businesses.length === 0}
        <div class="p-6 text-center">
          <p class="text-gray-500">No businesses found</p>
        </div>
      {:else}
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              {#each businesses as business}
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img class="h-10 w-10 rounded-full" src={business.logo || "/placeholder.svg"} alt={business.name} />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{business.name}</div>
                        <div class="text-sm text-gray-500">{business.email}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{business.ownerName}</div>
                    <div class="text-sm text-gray-500">{business.ownerEmail}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {#if business.status === "pending"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending
                      </span>
                    {:else if business.status === "approved"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Approved
                      </span>
                    {:else if business.status === "rejected"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Rejected
                      </span>
                    {/if}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {formatDate(business.createdAt)}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button 
                        class="text-primary hover:text-primary-dark"
                        on:click={() => viewBusinessDetails(business.id)}
                        title="View Details"
                      >
                        <Eye class="h-5 w-5" />
                      </button>
                      
                      {#if business.status === "pending"}
                        <button 
                          class="text-green-600 hover:text-green-800"
                          on:click={() => openApproveModal(business)}
                          title="Approve Business"
                        >
                          <Check class="h-5 w-5" />
                        </button>
                        
                        <button 
                          class="text-red-600 hover:text-red-800"
                          on:click={() => openRejectModal(business)}
                          title="Reject Business"
                        >
                          <X class="h-5 w-5" />
                        </button>
                      {/if}
                      
                      <button 
                        class="text-red-600 hover:text-red-800"
                        on:click={() => openDeleteModal(business)}
                        title="Delete Business"
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
          <h3 class="text-lg font-medium mb-4">Delete Business</h3>
          <p class="mb-4">Are you sure you want to delete <strong>{businessToDelete?.name}</strong>? This action cannot be undone.</p>
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
              on:click={handleDeleteBusiness}
              disabled={actionInProgress}
            >
              {actionInProgress ? 'Deleting...' : 'Delete'}
            </button>
          </div>
        </div>
      </div>
    {/if}
  
    <!-- Approve Modal -->
    {#if showApproveModal}
      <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium mb-4">Approve Business</h3>
          <p class="mb-4">Are you sure you want to approve <strong>{businessToUpdate?.name}</strong>? This will allow them to start selling products on the platform.</p>
          <div class="flex justify-end space-x-3">
            <button 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              on:click={closeApproveModal}
              disabled={actionInProgress}
            >
              Cancel
            </button>
            <button 
              class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
              on:click={handleApproveBusiness}
              disabled={actionInProgress}
            >
              {actionInProgress ? 'Approving...' : 'Approve'}
            </button>
          </div>
        </div>
      </div>
    {/if}
  
    <!-- Reject Modal -->
    {#if showRejectModal}
      <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium mb-4">Reject Business</h3>
          <p class="mb-4">Please provide a reason for rejecting <strong>{businessToUpdate?.name}</strong>:</p>
          <textarea
            class="w-full p-2 border rounded-md focus:ring-2 focus:ring-primary focus:border-primary mb-4"
            rows="3"
            placeholder="Reason for rejection"
            bind:value={rejectionReason}
          ></textarea>
          <div class="flex justify-end space-x-3">
            <button 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              on:click={closeRejectModal}
              disabled={actionInProgress}
            >
              Cancel
            </button>
            <button 
              class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
              on:click={handleRejectBusiness}
              disabled={actionInProgress || !rejectionReason.trim()}
            >
              {actionInProgress ? 'Rejecting...' : 'Reject'}
            </button>
          </div>
        </div>
      </div>
    {/if}
  </div>
  