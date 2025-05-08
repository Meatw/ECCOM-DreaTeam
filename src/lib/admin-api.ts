import type {
    User,
    Business,
    Product,
    ReportedItem,
    AdminStats,
    AdminPermission,
    BusinessStatus,
    ReportReason,
  } from "./types"
  
  // Base URL for API
  const API_BASE_URL = "/api/admin"
  
  // Fetch admin dashboard statistics
  export async function fetchAdminStats(): Promise<AdminStats> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800))
  
    // Mock admin stats
    const mockStats: AdminStats = {
      totalUsers: 1250,
      totalSellers: 120,
      totalCustomers: 1100,
      totalAdmins: 30,
      totalProducts: 3500,
      totalOrders: 8750,
      totalRevenue: 425000,
      pendingBusinessApprovals: 15,
      pendingReports: 8,
      newUsersThisMonth: 75,
      newOrdersThisMonth: 450,
      revenueThisMonth: 22500,
    }
  
    return mockStats
  }
  
  // Fetch users with optional filters
  export async function fetchUsers(
    options: {
      role?: string
      status?: string
      search?: string
      sortBy?: string
      page?: number
      limit?: number
    } = {},
  ): Promise<{ users: User[]; total: number }> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 600))
  
    // Generate mock users
    const mockUsers: User[] = Array.from({ length: 50 }, (_, i) => {
      const roles: ("customer" | "seller" | "admin" | "moderator")[] = ["customer", "seller", "admin", "moderator"]
      const statuses: ("active" | "suspended" | "banned")[] = ["active", "suspended", "banned"]
      const role = roles[Math.floor(Math.random() * (i < 40 ? 2 : 4))] // More customers and sellers
  
      // Generate date within the last year
      const date = new Date()
      date.setDate(date.getDate() - Math.floor(Math.random() * 365))
  
      return {
        id: i + 1,
        fullName: `User ${i + 1}`,
        email: `user${i + 1}@example.com`,
        role,
        status: statuses[Math.floor(Math.random() * (i < 45 ? 1 : 3))], // Most users are active
        createdAt: date.toISOString(),
        permissions:
          role === "admin" || role === "moderator"
            ? (["users", "businesses", "content", "reports"].slice(
                0,
                Math.floor(Math.random() * 4) + 1,
              ) as AdminPermission[])
            : undefined,
      }
    })
  
    // Apply filters
    let filteredUsers = [...mockUsers]
  
    if (options.role) {
      filteredUsers = filteredUsers.filter((u) => u.role === options.role)
    }
  
    if (options.status) {
      filteredUsers = filteredUsers.filter((u) => u.status === options.status)
    }
  
    if (options.search) {
      const searchLower = options.search.toLowerCase()
      filteredUsers = filteredUsers.filter(
        (u) =>
          u.fullName.toLowerCase().includes(searchLower) ||
          u.email.toLowerCase().includes(searchLower) ||
          u.id.toString().includes(searchLower),
      )
    }
  
    // Apply sorting
    if (options.sortBy) {
      switch (options.sortBy) {
        case "name-asc":
          filteredUsers.sort((a, b) => a.fullName.localeCompare(b.fullName))
          break
        case "name-desc":
          filteredUsers.sort((a, b) => b.fullName.localeCompare(a.fullName))
          break
        case "date-asc":
          filteredUsers.sort((a, b) => new Date(a.createdAt!).getTime() - new Date(b.createdAt!).getTime())
          break
        case "date-desc":
          filteredUsers.sort((a, b) => new Date(b.createdAt!).getTime() - new Date(a.createdAt!).getTime())
          break
      }
    } else {
      // Default sort by ID
      filteredUsers.sort((a, b) => b.id - a.id)
    }
  
    // Apply pagination
    const page = options.page || 1
    const limit = options.limit || 10
    const startIndex = (page - 1) * limit
    const endIndex = startIndex + limit
    const paginatedUsers = filteredUsers.slice(startIndex, endIndex)
  
    return {
      users: paginatedUsers,
      total: filteredUsers.length,
    }
  }
  
  // Fetch a single user by ID
  export async function fetchUserById(id: number): Promise<User | null> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 400))
  
    // Generate a mock user
    const roles: ("customer" | "seller" | "admin" | "moderator")[] = ["customer", "seller", "admin", "moderator"]
    const statuses: ("active" | "suspended" | "banned")[] = ["active", "suspended", "banned"]
    const role = roles[Math.floor(Math.random() * 4)]
  
    // Generate date within the last year
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 365))
  
    const mockUser: User = {
      id,
      fullName: `User ${id}`,
      email: `user${id}@example.com`,
      role,
      status: statuses[Math.floor(Math.random() * 3)],
      createdAt: date.toISOString(),
      permissions:
        role === "admin" || role === "moderator"
          ? (["users", "businesses", "content", "reports"].slice(
              0,
              Math.floor(Math.random() * 4) + 1,
            ) as AdminPermission[])
          : undefined,
    }
  
    return mockUser
  }
  
  // Update user status
  export async function updateUserStatus(userId: number, status: "active" | "suspended" | "banned"): Promise<User> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate updating a user
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800))
  
    // Get the user
    const user = await fetchUserById(userId)
  
    if (!user) {
      throw new Error("User not found")
    }
  
    // Update the user
    const updatedUser: User = {
      ...user,
      status,
    }
  
    return updatedUser
  }
  
  // Delete a user
  export async function deleteUser(userId: number): Promise<boolean> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate deleting a user
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000))
  
    // Return success
    return true
  }
  
  // Create a new admin user
  export async function createAdminUser(userData: {
    fullName: string
    email: string
    password: string
    role: "admin" | "moderator"
    permissions: AdminPermission[]
  }): Promise<User> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate creating a user
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1200))
  
    // Create a new user with mock data
    const newUser: User = {
      id: Math.floor(Math.random() * 1000) + 1000,
      fullName: userData.fullName,
      email: userData.email,
      role: userData.role,
      status: "active",
      createdAt: new Date().toISOString(),
      permissions: userData.permissions,
    }
  
    return newUser
  }
  
  // Update admin permissions
  export async function updateAdminPermissions(userId: number, permissions: AdminPermission[]): Promise<User> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate updating a user
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800))
  
    // Get the user
    const user = await fetchUserById(userId)
  
    if (!user) {
      throw new Error("User not found")
    }
  
    // Update the user
    const updatedUser: User = {
      ...user,
      permissions,
    }
  
    return updatedUser
  }
  
  // Fetch businesses with optional filters
  export async function fetchBusinesses(
    options: {
      status?: BusinessStatus
      search?: string
      sortBy?: string
      page?: number
      limit?: number
    } = {},
  ): Promise<{ businesses: Business[]; total: number }> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 600))
  
    // Generate mock businesses
    const mockBusinesses: Business[] = Array.from({ length: 40 }, (_, i) => {
      const statuses: BusinessStatus[] = ["pending", "approved", "rejected"]
      // More pending businesses for the first few
      const status = i < 15 ? (i < 8 ? "pending" : statuses[Math.floor(Math.random() * 3)]) : "approved"
  
      // Generate date within the last year
      const createdDate = new Date()
      createdDate.setDate(createdDate.getDate() - Math.floor(Math.random() * 365))
  
      // Generate approval/rejection date if applicable
      let approvedAt: string | undefined
      let rejectedAt: string | undefined
      let rejectionReason: string | undefined
  
      if (status === "approved") {
        const approvalDate = new Date(createdDate)
        approvalDate.setDate(approvalDate.getDate() + Math.floor(Math.random() * 7) + 1)
        approvedAt = approvalDate.toISOString()
      } else if (status === "rejected") {
        const rejectionDate = new Date(createdDate)
        rejectionDate.setDate(rejectionDate.getDate() + Math.floor(Math.random() * 7) + 1)
        rejectedAt = rejectionDate.toISOString()
        rejectionReason = [
          "Incomplete business information",
          "Invalid business documents",
          "Suspicious activity",
          "Duplicate business",
          "Violates platform policies",
        ][Math.floor(Math.random() * 5)]
      }
  
      const ownerId = i + 100
  
      return {
        id: i + 1,
        name: `Business ${i + 1}`,
        description: `This is a detailed description for business ${i + 1}. We specialize in providing high-quality products to our customers.`,
        logo: `/placeholder.svg?height=100&width=100&text=Logo+${i + 1}`,
        coverImage: `/placeholder.svg?height=300&width=800&text=Business+${i + 1}`,
        ownerId,
        ownerName: `User ${ownerId}`,
        ownerEmail: `user${ownerId}@example.com`,
        rating: Math.floor(Math.random() * 50) / 10 + 1,
        reviewCount: Math.floor(Math.random() * 1000) + 1,
        categories: ["Electronics", "Clothing", "Home & Kitchen"].slice(0, Math.floor(Math.random() * 3) + 1),
        address: `123 Business St, City, Country`,
        phoneNumber: `+1 234 567 890${i}`,
        email: `business${i + 1}@example.com`,
        status,
        createdAt: createdDate.toISOString(),
        approvedAt,
        rejectedAt,
        rejectionReason,
      }
    })
  
    // Apply filters
    let filteredBusinesses = [...mockBusinesses]
  
    if (options.status) {
      filteredBusinesses = filteredBusinesses.filter((b) => b.status === options.status)
    }
  
    if (options.search) {
      const searchLower = options.search.toLowerCase()
      filteredBusinesses = filteredBusinesses.filter(
        (b) =>
          b.name.toLowerCase().includes(searchLower) ||
          b.email.toLowerCase().includes(searchLower) ||
          b.ownerName?.toLowerCase().includes(searchLower) ||
          b.ownerEmail?.toLowerCase().includes(searchLower) ||
          b.id.toString().includes(searchLower),
      )
    }
  
    // Apply sorting
    if (options.sortBy) {
      switch (options.sortBy) {
        case "name-asc":
          filteredBusinesses.sort((a, b) => a.name.localeCompare(b.name))
          break
        case "name-desc":
          filteredBusinesses.sort((a, b) => b.name.localeCompare(a.name))
          break
        case "date-asc":
          filteredBusinesses.sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime())
          break
        case "date-desc":
          filteredBusinesses.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
          break
      }
    } else {
      // Default sort by creation date (newest first)
      filteredBusinesses.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
    }
  
    // Apply pagination
    const page = options.page || 1
    const limit = options.limit || 10
    const startIndex = (page - 1) * limit
    const endIndex = startIndex + limit
    const paginatedBusinesses = filteredBusinesses.slice(startIndex, endIndex)
  
    return {
      businesses: paginatedBusinesses,
      total: filteredBusinesses.length,
    }
  }
  
  // Fetch a single business by ID
  export async function fetchBusinessById(id: number): Promise<Business | null> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 400))
  
    // Generate a mock business
    const statuses: BusinessStatus[] = ["pending", "approved", "rejected"]
    const status = statuses[Math.floor(Math.random() * 3)]
  
    // Generate date within the last year
    const createdDate = new Date()
    createdDate.setDate(createdDate.getDate() - Math.floor(Math.random() * 365))
  
    // Generate approval/rejection date if applicable
    let approvedAt: string | undefined
    let rejectedAt: string | undefined
    let rejectionReason: string | undefined
  
    if (status === "approved") {
      const approvalDate = new Date(createdDate)
      approvalDate.setDate(approvalDate.getDate() + Math.floor(Math.random() * 7) + 1)
      approvedAt = approvalDate.toISOString()
    } else if (status === "rejected") {
      const rejectionDate = new Date(createdDate)
      rejectionDate.setDate(rejectionDate.getDate() + Math.floor(Math.random() * 7) + 1)
      rejectedAt = rejectionDate.toISOString()
      rejectionReason = [
        "Incomplete business information",
        "Invalid business documents",
        "Suspicious activity",
        "Duplicate business",
        "Violates platform policies",
      ][Math.floor(Math.random() * 5)]
    }
  
    const ownerId = id + 100
  
    const mockBusiness: Business = {
      id,
      name: `Business ${id}`,
      description: `This is a detailed description for business ${id}. We specialize in providing high-quality products to our customers.`,
      logo: `/placeholder.svg?height=100&width=100&text=Logo+${id}`,
      coverImage: `/placeholder.svg?height=300&width=800&text=Business+${id}`,
      ownerId,
      ownerName: `User ${ownerId}`,
      ownerEmail: `user${ownerId}@example.com`,
      rating: Math.floor(Math.random() * 50) / 10 + 1,
      reviewCount: Math.floor(Math.random() * 1000) + 1,
      categories: ["Electronics", "Clothing", "Home & Kitchen"].slice(0, Math.floor(Math.random() * 3) + 1),
      address: `123 Business St, City, Country`,
      phoneNumber: `+1 234 567 890${id}`,
      email: `business${id}@example.com`,
      status,
      createdAt: createdDate.toISOString(),
      approvedAt,
      rejectedAt,
      rejectionReason,
    }
  
    return mockBusiness
  }
  
  // Update business status
  export async function updateBusinessStatus(
    businessId: number,
    status: BusinessStatus,
    rejectionReason?: string,
  ): Promise<Business> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate updating a business
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800))
  
    // Get the business
    const business = await fetchBusinessById(businessId)
  
    if (!business) {
      throw new Error("Business not found")
    }
  
    // Update the business
    const updatedBusiness: Business = {
      ...business,
      status,
      approvedAt: status === "approved" ? new Date().toISOString() : business.approvedAt,
      rejectedAt: status === "rejected" ? new Date().toISOString() : business.rejectedAt,
      rejectionReason: status === "rejected" ? rejectionReason : business.rejectionReason,
    }
  
    return updatedBusiness
  }
  
  // Delete a business
  export async function deleteBusiness(businessId: number): Promise<boolean> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate deleting a business
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000))
  
    // Return success
    return true
  }
  
  // Fetch reported items with optional filters
  export async function fetchReportedItems(
    options: {
      itemType?: "product" | "review" | "user" | "business"
      status?: "pending" | "reviewed" | "actioned" | "dismissed"
      search?: string
      sortBy?: string
      page?: number
      limit?: number
    } = {},
  ): Promise<{ reports: ReportedItem[]; total: number }> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 600))
  
    // Generate mock reported items
    const mockReports: ReportedItem[] = Array.from({ length: 30 }, (_, i) => {
      const itemTypes: ("product" | "review" | "user" | "business")[] = ["product", "review", "user", "business"]
      const itemType = itemTypes[Math.floor(Math.random() * 4)]
  
      const reasons: ReportReason[] = ["inappropriate", "counterfeit", "misleading", "offensive", "other"]
      const reason = reasons[Math.floor(Math.random() * 5)]
  
      const statuses: ("pending" | "reviewed" | "actioned" | "dismissed")[] = [
        "pending",
        "reviewed",
        "actioned",
        "dismissed",
      ]
      // More pending reports for the first few
      const status = i < 10 ? "pending" : statuses[Math.floor(Math.random() * 4)]
  
      // Generate date within the last 30 days
      const createdDate = new Date()
      createdDate.setDate(createdDate.getDate() - Math.floor(Math.random() * 30))
  
      // Generate review date if applicable
      let reviewedAt: string | undefined
      let reviewedBy: number | undefined
  
      if (status !== "pending") {
        const reviewDate = new Date(createdDate)
        reviewDate.setDate(reviewDate.getDate() + Math.floor(Math.random() * 3) + 1)
        reviewedAt = reviewDate.toISOString()
        reviewedBy = Math.floor(Math.random() * 5) + 1 // Admin ID
      }
  
      const itemId = Math.floor(Math.random() * 100) + 1
      const reporterId = Math.floor(Math.random() * 100) + 1
  
      // Mock item data based on type
      let item: any
  
      if (itemType === "product") {
        item = {
          id: itemId,
          name: `Product ${itemId}`,
          description: `This is a description for product ${itemId}.`,
          price: Math.floor(Math.random() * 10000) / 100 + 100,
          imageUrl: `/placeholder.svg?height=200&width=200&text=Product+${itemId}`,
          category: ["Electronics", "Clothing", "Home & Kitchen"][Math.floor(Math.random() * 3)],
          sellerId: Math.floor(Math.random() * 10) + 1,
          sellerName: `Seller ${Math.floor(Math.random() * 10) + 1}`,
          status: status === "actioned" ? "removed" : "reported",
        }
      }
  
      return {
        id: i + 1,
        itemType,
        itemId,
        reporterId,
        reporterName: `User ${reporterId}`,
        reason,
        description: `This ${itemType} contains ${reason} content that violates the platform's policies.`,
        status,
        createdAt: createdDate.toISOString(),
        reviewedAt,
        reviewedBy,
        item: itemType === "product" ? item : undefined,
      }
    })
  
    // Apply filters
    let filteredReports = [...mockReports]
  
    if (options.itemType) {
      filteredReports = filteredReports.filter((r) => r.itemType === options.itemType)
    }
  
    if (options.status) {
      filteredReports = filteredReports.filter((r) => r.status === options.status)
    }
  
    if (options.search) {
      const searchLower = options.search.toLowerCase()
      filteredReports = filteredReports.filter(
        (r) =>
          r.description.toLowerCase().includes(searchLower) ||
          r.reporterName.toLowerCase().includes(searchLower) ||
          r.id.toString().includes(searchLower) ||
          r.itemId.toString().includes(searchLower),
      )
    }
  
    // Apply sorting
    if (options.sortBy) {
      switch (options.sortBy) {
        case "date-asc":
          filteredReports.sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime())
          break
        case "date-desc":
          filteredReports.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
          break
      }
    } else {
      // Default sort by creation date (newest first)
      filteredReports.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
    }
  
    // Apply pagination
    const page = options.page || 1
    const limit = options.limit || 10
    const startIndex = (page - 1) * limit
    const endIndex = startIndex + limit
    const paginatedReports = filteredReports.slice(startIndex, endIndex)
  
    return {
      reports: paginatedReports,
      total: filteredReports.length,
    }
  }
  
  // Fetch a single reported item by ID
  export async function fetchReportedItemById(id: number): Promise<ReportedItem | null> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll return mock data
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 400))
  
    // Generate a mock reported item
    const itemTypes: ("product" | "review" | "user" | "business")[] = ["product", "review", "user", "business"]
    const itemType = itemTypes[Math.floor(Math.random() * 4)]
  
    const reasons: ReportReason[] = ["inappropriate", "counterfeit", "misleading", "offensive", "other"]
    const reason = reasons[Math.floor(Math.random() * 5)]
  
    const statuses: ("pending" | "reviewed" | "actioned" | "dismissed")[] = [
      "pending",
      "reviewed",
      "actioned",
      "dismissed",
    ]
    const status = statuses[Math.floor(Math.random() * 4)]
  
    // Generate date within the last 30 days
    const createdDate = new Date()
    createdDate.setDate(createdDate.getDate() - Math.floor(Math.random() * 30))
  
    // Generate review date if applicable
    let reviewedAt: string | undefined
    let reviewedBy: number | undefined
  
    if (status !== "pending") {
      const reviewDate = new Date(createdDate)
      reviewDate.setDate(reviewDate.getDate() + Math.floor(Math.random() * 3) + 1)
      reviewedAt = reviewDate.toISOString()
      reviewedBy = Math.floor(Math.random() * 5) + 1 // Admin ID
    }
  
    const itemId = Math.floor(Math.random() * 100) + 1
    const reporterId = Math.floor(Math.random() * 100) + 1
  
    // Mock item data based on type
    let item: any
  
    if (itemType === "product") {
      item = {
        id: itemId,
        name: `Product ${itemId}`,
        description: `This is a description for product ${itemId}.`,
        price: Math.floor(Math.random() * 10000) / 100 + 100,
        imageUrl: `/placeholder.svg?height=200&width=200&text=Product+${itemId}`,
        category: ["Electronics", "Clothing", "Home & Kitchen"][Math.floor(Math.random() * 3)],
        sellerId: Math.floor(Math.random() * 10) + 1,
        sellerName: `Seller ${Math.floor(Math.random() * 10) + 1}`,
        status:
  \
  Math.random() * 10) + 1,
        sellerName: `Seller ${Math.floor(Math.random() * 10) + 1}`,
        status: status === "actioned" ? "removed" : "reported",
      }
    } else if (itemType === "review") {
      item = {
        id: itemId,
        productId: Math.floor(Math.random() * 100) + 1,
        customerId: reporterId,
        customerName: `User ${reporterId}`,
        rating: Math.floor(Math.random() * 5) + 1,
        title: "Product Review",
        comment: `This is a review that contains ${reason} content.`,
        createdAt: new Date(createdDate.getTime() - 86400000).toISOString(),
      }
    } else if (itemType === "user") {
      item = {
        id: itemId,
        fullName: `User ${itemId}`,
        email: `user${itemId}@example.com`,
        role: "customer",
        status: status === "actioned" ? "banned" : "active",
      }
    } else if (itemType === "business") {
      item = {
        id: itemId,
        name: `Business ${itemId}`,
        description: `This is a business that contains ${reason} content.`,
        ownerId: Math.floor(Math.random() * 100) + 1,
        status: status === "actioned" ? "rejected" : "approved",
      }
    }
  
    const mockReport: ReportedItem = {
      id,
      itemType,
      itemId,
      reporterId,
      reporterName: `User ${reporterId}`,
      reason,
      description: `This ${itemType} contains ${reason} content that violates the platform's policies.`,
      status,
      createdAt: createdDate.toISOString(),
      reviewedAt,
      reviewedBy,
      item,
    }
  
    return mockReport
  }
  
  // Update reported item status
  export async function updateReportedItemStatus(
    reportId: number,
    status: "reviewed" | "actioned" | "dismissed",
  ): Promise<ReportedItem> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate updating a report
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 800))
  
    // Get the report
    const report = await fetchReportedItemById(reportId)
  
    if (!report) {
      throw new Error("Report not found")
    }
  
    // Update the report
    const updatedReport: ReportedItem = {
      ...report,
      status,
      reviewedAt: new Date().toISOString(),
      reviewedBy: 1, // Current admin ID
    }
  
    // If actioned, update the item status
    if (status === "actioned" && updatedReport.item) {
      if (updatedReport.itemType === "product") {
        ;(updatedReport.item as Product).status = "removed"
      } else if (updatedReport.itemType === "user") {
        ;(updatedReport.item as User).status = "banned"
      } else if (updatedReport.itemType === "business") {
        ;(updatedReport.item as Business).status = "rejected"
      }
    }
  
    return updatedReport
  }
  
  // Remove a reported item
  export async function removeReportedItem(reportId: number): Promise<boolean> {
    // In a real app, this would call your PHP backend
    // For demo purposes, we'll simulate removing a reported item
  
    // Simulate API call delay
    await new Promise((resolve) => setTimeout(resolve, 1000))
  
    // Update the report status to actioned
    await updateReportedItemStatus(reportId, "actioned")
  
    // Return success
    return true
  }
  