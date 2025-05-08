<script lang="ts">
    import { onMount } from "svelte"
    import { goto } from "$app/navigation"
    import { fetchReportedItems, updateReportedItemStatus, removeReportedItem } from "$lib/admin-api"
    import type { ReportedItem } from "$lib/types"
    import { Search, Filter, Eye, Trash2, CheckCircle, XCircle } from "lucide-svelte"
  
    let reports: ReportedItem[] = []
    let totalReports = 0
    let loading = true
    let error: string | null = null
    let currentPage = 1
    let itemsPerPage = 10
    let searchQuery = ""
    let itemTypeFilter: string = ""
    let statusFilter: string = ""
    let sortBy = "date-desc"
    let showActionModal = false
    let reportToAction: ReportedItem | null = null
    let actionType: "dismiss" | "remove" = "dismiss"
    let actionInProgress = false
    let totalPages: number
    let pageNumbers: number[]
  
    onMount(async () => {
      const url = new URL(window.location.href)
      const itemType = url.searchParams.get("itemType")
      const status = url.searchParams.get("status")
      
      if (itemType) itemTypeFilter = itemType
      if (status) statusFilter = status
      
      await loadReports()
    })
  
    async function loadReports() {
      loading = true
      error = null
      
      try {
        const result = await fetchReportedItems({
          itemType: itemTypeFilter as any || undefined,
          status: statusFilter as any || undefined,
          search: searchQuery || undefined,
          sortBy,
          page: currentPage,
          limit: itemsPerPage
        })
        
        reports = result.reports
        totalReports = result.total
        loading = false
      } catch (err) {
        error = "Failed to load reports"
        loading = false
      }
    }
  
    function handleSearch() {
      currentPage = 1
      loadReports()
    }
  
    function handleFilterChange() {
      currentPage = 1
      loadReports()
    }
  
    function handleSortChange() {
      loadReports()
    }
  
    function handlePageChange(page: number) {
      currentPage = page
      loadReports()
    }
  
    function formatDate(dateString: string): string {
      const date = new Date(dateString)
      return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric"
      }).format(date)
    }
  
    function openActionModal(report: ReportedItem, action: "dismiss" | "remove") {
      reportToAction = report
      actionType = action
      showActionModal = true
    }
  
    function closeActionModal() {
      showActionModal = false
      reportToAction = null
    }
  
    async function handleReportAction() {
      if (!reportToAction) return
      
      actionInProgress = true
      
      try {
        if (actionType === "dismiss") {
          const updatedReport = await updateReportedItemStatus(reportToAction.id, "dismissed")
          reports = reports.map(r => r.id === updatedReport.id ? updatedReport : r)
        } else {
          await removeReportedItem(reportToAction.id)
          const updatedReport = await fetchReportedItems({ 
            id: reportToAction.id 
          } as any)
          reports = reports.map(r => r.id === reportToAction.id ? updatedReport.reports[0] : r)
        }
        
        closeActionModal()
      } catch (err) {
        error = `Failed to ${actionType} report`
      } finally {
        actionInProgress = false
      }
    }
  
    function viewReportDetails(id: number) {
      goto(`/admin/reports/${id}`)
    }
  
    function getItemTypeLabel(type: string): string {
      switch (type) {
        case "product": return "Product"
        case "review": return "Review"
        case "user": return "User"
        case "business": return "Business"
        default: return type
      }
    }
  
    function getReasonLabel(reason: string): string {
      switch (reason) {
        case "inappropriate": return "Inappropriate"
        case "counterfeit": return "Counterfeit"
        case "misleading": return "Misleading"
        case "offensive": return "Offensive"
        case "other": return "Other"
        default: return reason
      }
    }
  
    // Calculate total pages
    $: totalPages = Math.ceil(totalReports / itemsPerPage)
    
    // Generate page numbers for pagination
    $: pageNumbers = Array.from({ length: totalPages }, (_, i) => i + 1)
  </script>
  
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold">Reported Items</h1>
      <a href="/admin/dashboard" class="text-primary hover:underline">Back to Dashboard</a>
    </div>
  
    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200 mb-6">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
          <div class="relative">
            <input
              type="text"
              placeholder="Search reports..."
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
              bind:value={itemTypeFilter}
              on:change={handleFilterChange}
            >
              <option value="">All Types</option>
              <option value="product">Products</option>
              <option value="review">Reviews</option>
              <option value="user">Users</option>
              <option value="business">Businesses</option>
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
              <option value="pending">Pending</option>
              <option value="reviewed">Reviewed</option>
              <option value="actioned">Actioned</option>
              <option value="dismissed">Dismissed</option>
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
  
    <!-- Reports Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
      {#if loading}
        <div class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-primary"></div>
        </div>
      {:else if reports.length === 0}
        <div class="p-6 text-center">
          <p class="text-gray-500">No reports found</p>
        </div>
      {:else}
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              {#each reports as report}
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">#{report.id}</div>
                        <div class="text-sm text-gray-500">By {report.reporterName}</div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                      {getItemTypeLabel(report.itemType)}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                      {getReasonLabel(report.reason)}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    {#if report.status === "pending"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                        Pending
                      </span>
                    {:else if report.status === "reviewed"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        Reviewed
                      </span>
                    {:else if report.status === "actioned"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                        Actioned
                      </span>
                    {:else if report.status === "dismissed"}
                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Dismissed
                      </span>
                    {/if}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {formatDate(report.createdAt)}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                      <button 
                        class="text-primary hover:text-primary-dark"
                        on:click={() => viewReportDetails(report.id)}
                        title="View Details"
                      >
                        <Eye class="h-5 w-5" />
                      </button>
                      
                      {#if report.status === "pending"}
                        <button 
                          class="text-green-600 hover:text-green-800"
                          on:click={() => openActionModal(report, "dismiss")}
                          title="Dismiss Report"
                        >
                          <CheckCircle class="h-5 w-5" />
                        </button>
                        
                        <button 
                          class="text-red-600 hover:text-red-800"
                          on:click={() => openActionModal(report, "remove")}
                          title="Remove Item"
                        >
                          <Trash2 class="h-5 w-5" />
                        </button>
                      {/if}
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
  
    <!-- Action Modal -->
    {#if showActionModal}
      <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
          <h3 class="text-lg font-medium mb-4">
            {actionType === "dismiss" ? "Dismiss Report" : "Remove Reported Item"}
          </h3>
          <p class="mb-4">
            {#if actionType === "dismiss"}
              Are you sure you want to dismiss this report? This will mark the report as reviewed but take no action against the reported item.
            {:else}
              Are you sure you want to remove this {reportToAction?.itemType}? This will mark the report as actioned and remove the reported item from the platform.
            {/if}
          </p>
          <div class="flex justify-end space-x-3">
            <button 
              class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
              on:click={closeActionModal}
              disabled={actionInProgress}
            >
              Cancel
            </button>
            <button 
              class="px-4 py-2 {actionType === 'dismiss' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'} text-white rounded-md"
              on:click={handleReportAction}
              disabled={actionInProgress}
            >
              {actionInProgress ? 'Processing...' : actionType === "dismiss" ? "Dismiss" : "Remove"}
            </button>
          </div>
        </div>
      </div>
    {/if}
  </div>
  