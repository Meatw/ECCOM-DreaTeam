import { goto } from "$app/navigation"

// Define user types
export type UserRole = "customer" | "seller" | "admin"

export interface User {
  id: number
  fullName: string
  email: string
  role: UserRole
  token?: string
}

// Store the current user in localStorage
export function setCurrentUser(user: User): void {
  // Store only necessary user data (no sensitive info)
  const userData = {
    id: user.id,
    fullName: user.fullName,
    email: user.email,
    role: user.role,
    token: user.token,
  }

  localStorage.setItem("quickbuy_user", JSON.stringify(userData))
  localStorage.setItem("quickbuy_token", user.token || "")
}

// Get the current user from localStorage
export function getCurrentUser(): User | null {
  const userJson = localStorage.getItem("quickbuy_user")
  if (!userJson) return null

  try {
    return JSON.parse(userJson) as User
  } catch (error) {
    console.error("Error parsing user data:", error)
    return null
  }
}

// Check if user is authenticated
export function isAuthenticated(): boolean {
  return !!localStorage.getItem("quickbuy_token")
}

// Check if user has specific role
export function hasRole(role: UserRole): boolean {
  const user = getCurrentUser()
  return user !== null && user.role === role
}

// Logout function
export function logout(): void {
  localStorage.removeItem("quickbuy_user")
  localStorage.removeItem("quickbuy_token")
  goto("/login")
}

// Login function
export async function login(email: string, password: string): Promise<User> {
  try {
    // In a real app, this would call your PHP backend
    const response = await fetch("/api/auth/login", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ email, password }),
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || "Login failed")
    }

    const userData = await response.json()
    setCurrentUser(userData.user)
    return userData.user
  } catch (error) {
    console.error("Login error:", error)
    throw error
  }
}

// Register function
export async function register(userData: {
  fullName: string
  email: string
  password: string
  role: UserRole
  storeName?: string
  storeDescription?: string
  phoneNumber?: string
}): Promise<User> {
  try {
    // In a real app, this would call your PHP backend
    const response = await fetch("/api/auth/register", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    })

    if (!response.ok) {
      const errorData = await response.json()
      throw new Error(errorData.message || "Registration failed")
    }

    const responseData = await response.json()
    return responseData.user
  } catch (error) {
    console.error("Registration error:", error)
    throw error
  }
}

// Protected route middleware (for client-side route protection)
export function requireAuth(role?: UserRole): void {
  if (!isAuthenticated()) {
    goto("/login")
    return
  }

  if (role && !hasRole(role)) {
    goto("/unauthorized")
    return
  }
}
