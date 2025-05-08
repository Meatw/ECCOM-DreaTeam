// Define types for the application

export type UserRole = "customer" | "seller" | "admin" | "moderator"
export type AdminPermission = "full" | "users" | "businesses" | "content" | "reports"
export type BusinessStatus = "pending" | "approved" | "rejected"
export type ReportReason = "inappropriate" | "counterfeit" | "misleading" | "offensive" | "other"

export interface User {
  id: number
  fullName: string
  email: string
  role: UserRole
  token?: string
  status?: "active" | "suspended" | "banned"
  createdAt?: string
  permissions?: AdminPermission[] // Only for admin users
}

export interface Product {
  id: number
  name: string
  description: string
  price: number
  imageUrl: string
  category: string
  sellerId: number
  sellerName: string
  rating: number
  reviewCount: number
  stock: number
  status?: "active" | "reported" | "removed"
}

export interface CartItem {
  productId: number
  product: Product
  quantity: number
}

export interface Cart {
  items: CartItem[]
  totalItems: number
  subtotal: number
}

export type OrderStatus = "pending" | "processing" | "shipped" | "delivered" | "cancelled"

export interface Order {
  id: number
  customerId: number
  customerName?: string
  customerEmail?: string
  items: CartItem[]
  totalAmount: number
  status: OrderStatus
  createdAt: string
  updatedAt: string
  shippingAddress: string
  paymentMethod: string
}

export interface Business {
  id: number
  name: string
  description: string
  logo: string
  coverImage: string
  ownerId: number
  ownerName?: string
  ownerEmail?: string
  rating: number
  reviewCount: number
  categories: string[]
  address: string
  phoneNumber: string
  email: string
  status: BusinessStatus
  createdAt: string
  approvedAt?: string
  rejectedAt?: string
  rejectionReason?: string
}

export interface ProductCategory {
  id: number
  name: string
  description: string
}

export interface Review {
  id: number
  productId: number
  customerId: number
  customerName: string
  rating: number
  title: string
  comment: string
  createdAt: string
  sellerResponse?: {
    comment: string
    createdAt: string
  }
}

export interface SellerStats {
  totalProducts: number
  totalOrders: number
  totalRevenue: number
  averageRating: number
  topSellingProducts: {
    product: Product
    salesCount: number
    revenue: number
  }[]
  salesByCategory: {
    category: string
    sales: number
  }[]
  monthlySales: {
    month: string
    year: number
    sales: number
  }[]
  pendingOrders: number
  lowStockProducts: number
}

export interface ReportedItem {
  id: number
  itemType: "product" | "review" | "user" | "business"
  itemId: number
  reporterId: number
  reporterName: string
  reason: ReportReason
  description: string
  status: "pending" | "reviewed" | "actioned" | "dismissed"
  createdAt: string
  reviewedAt?: string
  reviewedBy?: number
  item?: Product | Review | User | Business
}

export interface AdminStats {
  totalUsers: number
  totalSellers: number
  totalCustomers: number
  totalAdmins: number
  totalProducts: number
  totalOrders: number
  totalRevenue: number
  pendingBusinessApprovals: number
  pendingReports: number
  newUsersThisMonth: number
  newOrdersThisMonth: number
  revenueThisMonth: number
}
