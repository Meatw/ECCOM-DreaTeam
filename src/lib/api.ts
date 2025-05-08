import type { Product, Business, Order, Review, ProductCategory, SellerStats } from "./types"

// Base URL for API
const API_BASE_URL = "/api"

// Fetch products with optional filters
export async function fetchProducts(
  options: {
    category?: string
    search?: string
    minPrice?: number
    maxPrice?: number
    sortBy?: string
    page?: number
    limit?: number
  } = {},
): Promise<{ products: Product[]; total: number }> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Mock products data with reliable image URLs
  const mockProducts: Product[] = [
    // Product 1-10 with online images
    {
      id: 1,
      name: "Premium Wireless Headphones",
      description:
        "Experience superior sound quality with these premium wireless headphones. Features active noise cancellation, 30-hour battery life, and comfortable over-ear design.",
      price: 199.99,
      imageUrl: "https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 2,
      sellerName: "AudioTech Pro",
      rating: 4.8,
      reviewCount: 156,
      stock: 42,
    },
    {
      id: 2,
      name: "Classic Leather Jacket",
      description:
        "Timeless leather jacket crafted from premium full-grain leather. Features a comfortable fit, durable construction, and stylish design that never goes out of fashion.",
      price: 249.99,
      imageUrl: "https://images.pexels.com/photos/16170/pexels-photo.jpg?auto=compress&cs=tinysrgb&w=600",
      category: "Clothing",
      sellerId: 5,
      sellerName: "Fashion Forward",
      rating: 4.9,
      reviewCount: 87,
      stock: 15,
    },
    {
      id: 3,
      name: "Professional DSLR Camera",
      description:
        "High-performance DSLR camera with 24.2MP sensor, 4K video recording, and advanced autofocus system. Perfect for professional photographers and serious enthusiasts.",
      price: 1299.99,
      imageUrl: "https://images.pexels.com/photos/90946/pexels-photo-90946.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "PhotoPro Equipment",
      rating: 4.7,
      reviewCount: 112,
      stock: 8,
    },
    {
      id: 4,
      name: "Gourmet Coffee Maker",
      description:
        "Premium coffee maker with precision brewing technology, temperature control, and built-in grinder. Makes barista-quality coffee at home.",
      price: 179.99,
      imageUrl: "https://images.pexels.com/photos/6542521/pexels-photo-6542521.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: 4,
      sellerName: "Kitchen Essentials",
      rating: 4.6,
      reviewCount: 94,
      stock: 22,
    },
    {
      id: 5,
      name: "Luxury Wristwatch",
      description:
        "Elegant timepiece with Swiss movement, sapphire crystal, and genuine leather strap. Water-resistant to 100m and features luminous hands for nighttime visibility.",
      price: 499.99,
      imageUrl: "https://images.pexels.com/photos/9978722/pexels-photo-9978722.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Accessories",
      sellerId: 6,
      sellerName: "Luxury Timepieces",
      rating: 4.9,
      reviewCount: 76,
      stock: 5,
    },
    {
      id: 6,
      name: "Modern Ceiling Fan with Light",
      description:
        "Sleek 4-blade ceiling fan with integrated light fixture. Features chrome finish and gray blades. Perfect for modern homes and provides excellent air circulation with energy-efficient operation.",
      price: 129.99,
      imageUrl: "https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 2,
      sellerName: "Home Comfort Solutions",
      rating: 4.7,
      reviewCount: 128,
      stock: 45,
    },
    {
      id: 7,
      name: "MANABISHI High-Velocity Floor Fan",
      description:
        "Powerful floor fan with adjustable tilt and three speed settings. Features durable metal construction and quiet operation. Perfect for workshops, garages, or any space needing enhanced air circulation.",
      price: 49.99,
      imageUrl: "https://images.pexels.com/photos/5824901/pexels-photo-5824901.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 2,
      sellerName: "Home Comfort Solutions",
      rating: 4.5,
      reviewCount: 87,
      stock: 32,
    },
    {
      id: 8,
      name: "Ergonomic Office Chair",
      description:
        "Comfortable office chair with lumbar support, adjustable height, and breathable mesh back. Perfect for long work sessions.",
      price: 189.99,
      imageUrl: "https://images.pexels.com/photos/1957478/pexels-photo-1957478.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Furniture",
      sellerId: 7,
      sellerName: "Office Solutions",
      rating: 4.6,
      reviewCount: 103,
      stock: 18,
    },
    {
      id: 9,
      name: "Smart Fitness Watch",
      description:
        "Advanced fitness tracker with heart rate monitoring, GPS, sleep tracking, and 7-day battery life. Water-resistant to 50m.",
      price: 149.99,
      imageUrl: "https://images.pexels.com/photos/437037/pexels-photo-437037.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 8,
      sellerName: "FitTech",
      rating: 4.7,
      reviewCount: 215,
      stock: 30,
    },
    {
      id: 10,
      name: "Wireless Bluetooth Headphones",
      description: "Premium noise-cancelling headphones with 30-hour battery life and comfortable over-ear design.",
      price: 89.99,
      imageUrl: "https://images.pexels.com/photos/577769/pexels-photo-577769.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "AudioTech",
      rating: 4.8,
      reviewCount: 215,
      stock: 78,
    },
    // Products 11-25 with online images
    {
      id: 11,
      name: "Ultra HD Smart TV",
      description:
        "65-inch 4K Ultra HD Smart TV with HDR and built-in streaming apps. Enjoy stunning picture quality and smart features.",
      price: 799.99,
      imageUrl: "https://images.pexels.com/photos/6976094/pexels-photo-6976094.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 4,
      sellerName: "ElectroVision",
      rating: 4.6,
      reviewCount: 189,
      stock: 15,
    },
    {
      id: 12,
      name: "Designer Handbag",
      description:
        "Luxury designer handbag made from premium materials. Features multiple compartments and elegant design.",
      price: 349.99,
      imageUrl: "https://images.pexels.com/photos/1152077/pexels-photo-1152077.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Accessories",
      sellerId: 5,
      sellerName: "Fashion Forward",
      rating: 4.9,
      reviewCount: 76,
      stock: 8,
    },
    {
      id: 13,
      name: "Professional Blender",
      description:
        "High-performance blender with variable speed control and pulse function. Perfect for smoothies, soups, and more.",
      price: 129.99,
      imageUrl: "https://images.pexels.com/photos/1080173/pexels-photo-1080173.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: 4,
      sellerName: "Kitchen Essentials",
      rating: 4.5,
      reviewCount: 112,
      stock: 25,
    },
    {
      id: 14,
      name: "Mechanical Keyboard",
      description:
        "Premium mechanical keyboard with RGB backlighting and customizable keys. Designed for gamers and professionals.",
      price: 129.99,
      imageUrl: "https://images.pexels.com/photos/1772123/pexels-photo-1772123.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "TechGear",
      rating: 4.7,
      reviewCount: 143,
      stock: 30,
    },
    {
      id: 15,
      name: "Yoga Mat",
      description: "Non-slip yoga mat with alignment lines. Perfect for yoga, pilates, and other floor exercises.",
      price: 39.99,
      imageUrl: "https://images.pexels.com/photos/4498362/pexels-photo-4498362.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Sports & Outdoors",
      sellerId: 9,
      sellerName: "FitLife",
      rating: 4.4,
      reviewCount: 87,
      stock: 50,
    },
    {
      id: 16,
      name: "Stainless Steel Cookware Set",
      description: "10-piece stainless steel cookware set with non-stick coating. Includes pots, pans, and lids.",
      price: 199.99,
      imageUrl: "https://images.pexels.com/photos/5825576/pexels-photo-5825576.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: 4,
      sellerName: "Kitchen Essentials",
      rating: 4.8,
      reviewCount: 156,
      stock: 20,
    },
    {
      id: 17,
      name: "Wireless Gaming Mouse",
      description: "High-precision wireless gaming mouse with customizable buttons and RGB lighting.",
      price: 79.99,
      imageUrl: "https://images.pexels.com/photos/5082579/pexels-photo-5082579.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "TechGear",
      rating: 4.6,
      reviewCount: 98,
      stock: 35,
    },
    {
      id: 18,
      name: "Portable Bluetooth Speaker",
      description: "Waterproof portable Bluetooth speaker with 20-hour battery life and deep bass.",
      price: 69.99,
      imageUrl: "https://images.pexels.com/photos/1279107/pexels-photo-1279107.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "AudioTech",
      rating: 4.5,
      reviewCount: 123,
      stock: 40,
    },
    {
      id: 19,
      name: "Aromatherapy Diffuser",
      description:
        "Ultrasonic aromatherapy diffuser with LED lights and automatic shut-off. Perfect for home or office.",
      price: 49.99,
      imageUrl: "https://images.pexels.com/photos/4046718/pexels-photo-4046718.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: 10,
      sellerName: "Wellness Essentials",
      rating: 4.7,
      reviewCount: 87,
      stock: 25,
    },
    {
      id: 20,
      name: "Stylish Sunglasses",
      description: "Designer sunglasses with UV protection and polarized lenses. Lightweight and comfortable.",
      price: 129.99,
      imageUrl: "https://images.pexels.com/photos/701877/pexels-photo-701877.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Accessories",
      sellerId: 5,
      sellerName: "Fashion Forward",
      rating: 4.8,
      reviewCount: 65,
      stock: 15,
    },
    {
      id: 21,
      name: "Digital Drawing Tablet",
      description: "Professional drawing tablet with pressure sensitivity and customizable shortcut keys.",
      price: 249.99,
      imageUrl: "https://images.pexels.com/photos/6686455/pexels-photo-6686455.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 3,
      sellerName: "CreativeTech",
      rating: 4.9,
      reviewCount: 78,
      stock: 10,
    },
    {
      id: 22,
      name: "Insulated Water Bottle",
      description: "Double-walled insulated water bottle that keeps drinks cold for 24 hours or hot for 12 hours.",
      price: 34.99,
      imageUrl: "https://images.pexels.com/photos/1188649/pexels-photo-1188649.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Sports & Outdoors",
      sellerId: 9,
      sellerName: "OutdoorLife",
      rating: 4.7,
      reviewCount: 112,
      stock: 45,
    },
    {
      id: 23,
      name: "Organic Skincare Set",
      description:
        "Complete organic skincare set with cleanser, toner, moisturizer, and serum. Made with natural ingredients.",
      price: 89.99,
      imageUrl: "https://images.pexels.com/photos/3321416/pexels-photo-3321416.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Beauty",
      sellerId: 11,
      sellerName: "Natural Beauty",
      rating: 4.8,
      reviewCount: 95,
      stock: 20,
    },
    {
      id: 24,
      name: "Smart Home Security Camera",
      description:
        "Wireless security camera with motion detection, night vision, and two-way audio. Connects to your smartphone.",
      price: 79.99,
      imageUrl: "https://images.pexels.com/photos/3205735/pexels-photo-3205735.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: 12,
      sellerName: "SmartHome Solutions",
      rating: 4.6,
      reviewCount: 87,
      stock: 30,
    },
    {
      id: 25,
      name: "Adjustable Dumbbell Set",
      description:
        "Space-saving adjustable dumbbell set that replaces multiple dumbbells. Weight range from 5 to 52.5 pounds.",
      price: 299.99,
      imageUrl: "https://images.pexels.com/photos/4498151/pexels-photo-4498151.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Sports & Outdoors",
      sellerId: 9,
      sellerName: "FitLife",
      rating: 4.9,
      reviewCount: 76,
      stock: 15,
    },
  ]

  // Apply filters
  let filteredProducts = [...mockProducts]

  if (options.category) {
    filteredProducts = filteredProducts.filter((p) => p.category === options.category)
  }

  if (options.search) {
    const searchLower = options.search.toLowerCase()
    filteredProducts = filteredProducts.filter(
      (p) => p.name.toLowerCase().includes(searchLower) || p.description.toLowerCase().includes(searchLower),
    )
  }

  if (options.minPrice !== undefined) {
    filteredProducts = filteredProducts.filter((p) => p.price >= options.minPrice!)
  }

  if (options.maxPrice !== undefined) {
    filteredProducts = filteredProducts.filter((p) => p.price <= options.maxPrice!)
  }

  // Apply sorting
  if (options.sortBy) {
    switch (options.sortBy) {
      case "price-asc":
        filteredProducts.sort((a, b) => a.price - b.price)
        break
      case "price-desc":
        filteredProducts.sort((a, b) => b.price - a.price)
        break
      case "rating":
        filteredProducts.sort((a, b) => b.rating - a.rating)
        break
      case "newest":
        // For mock data, we'll just use the ID as a proxy for "newest"
        filteredProducts.sort((a, b) => b.id - a.id)
        break
    }
  }

  // Apply pagination
  const page = options.page || 1
  const limit = options.limit || 12
  const startIndex = (page - 1) * limit
  const endIndex = startIndex + limit
  const paginatedProducts = filteredProducts.slice(startIndex, endIndex)

  return {
    products: paginatedProducts,
    total: filteredProducts.length,
  }
}

// Fetch a single product by ID
export async function fetchProductById(id: number): Promise<Product | null> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Get all products
  const { products } = await fetchProducts({ limit: 100 })

  // Find the product with the matching ID
  const product = products.find((p) => p.id === id)

  return product || null
}

// Fetch business/seller by ID
export async function fetchBusinessById(id: number): Promise<Business | null> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Mock business data
  const mockBusiness: Business = {
    id,
    name: `Business ${id}`,
    description: `This is a detailed description for business ${id}. We specialize in providing high-quality products to our customers.`,
    logo: "https://images.pexels.com/photos/5668858/pexels-photo-5668858.jpeg?auto=compress&cs=tinysrgb&w=100",
    coverImage: "https://images.pexels.com/photos/3184292/pexels-photo-3184292.jpeg?auto=compress&cs=tinysrgb&w=800",
    ownerId: id,
    rating: Math.floor(Math.random() * 50) / 10 + 1,
    reviewCount: Math.floor(Math.random() * 1000) + 1,
    categories: ["Electronics", "Clothing", "Home & Kitchen"].slice(0, Math.floor(Math.random() * 3) + 1),
    address: `123 Business St, City, Country`,
    phoneNumber: `+1 234 567 890${id}`,
    email: `business${id}@example.com`,
  }

  return mockBusiness
}

// Fetch products by business/seller ID
export async function fetchProductsByBusinessId(businessId: number): Promise<Product[]> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Mock products data with proper images
  const mockProducts: Product[] = [
    {
      id: 100,
      name: `Product 1 from Business ${businessId}`,
      description: `This is a high-quality product from Business ${businessId}. Features premium materials and excellent craftsmanship.`,
      price: 108.98,
      imageUrl: "https://images.pexels.com/photos/1029243/pexels-photo-1029243.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 4.3,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 101,
      name: `Product 2 from Business ${businessId}`,
      description: `Premium offering from Business ${businessId}. Known for its durability and modern design.`,
      price: 122.6,
      imageUrl: "https://images.pexels.com/photos/404280/pexels-photo-404280.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 5.6,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 102,
      name: `Product 3 from Business ${businessId}`,
      description: `Bestselling item from Business ${businessId}. Customers love its versatility and value.`,
      price: 126.32,
      imageUrl: "https://images.pexels.com/photos/4226119/pexels-photo-4226119.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Clothing",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 5.2,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 103,
      name: `Product 4 from Business ${businessId}`,
      description: `Innovative solution from Business ${businessId}. Designed to make your life easier.`,
      price: 162.99,
      imageUrl: "https://images.pexels.com/photos/4195324/pexels-photo-4195324.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 2.6,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 104,
      name: `Product 5 from Business ${businessId}`,
      description: `Affordable quality from Business ${businessId}. Great performance at a reasonable price.`,
      price: 111.81,
      imageUrl: "https://images.pexels.com/photos/6044266/pexels-photo-6044266.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Home & Kitchen",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 2.7,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 105,
      name: `Product 6 from Business ${businessId}`,
      description: `Luxury item from Business ${businessId}. Elevate your lifestyle with this premium product.`,
      price: 107.91,
      imageUrl: "https://images.pexels.com/photos/1667088/pexels-photo-1667088.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Accessories",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 4.9,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 106,
      name: `Product 7 from Business ${businessId}`,
      description: `Exclusive release from Business ${businessId}. Limited quantities available.`,
      price: 178.14,
      imageUrl: "https://images.pexels.com/photos/1464625/pexels-photo-1464625.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Clothing",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 3.5,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
    {
      id: 107,
      name: `Product 8 from Business ${businessId}`,
      description: `Customer favorite from Business ${businessId}. Consistently receives top reviews.`,
      price: 113.39,
      imageUrl: "https://images.pexels.com/photos/4226733/pexels-photo-4226733.jpeg?auto=compress&cs=tinysrgb&w=600",
      category: "Electronics",
      sellerId: businessId,
      sellerName: `Business ${businessId}`,
      rating: 4.9,
      reviewCount: Math.floor(Math.random() * 100) + 50,
      stock: Math.floor(Math.random() * 50) + 10,
    },
  ]

  return mockProducts
}

// Create an order
export async function createOrder(orderData: {
  items: { productId: number; quantity: number }[]
  shippingAddress: string
  paymentMethod: string
}): Promise<Order> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 2000))

  // Mock order data
  const mockOrder: Order = {
    id: Math.floor(Math.random() * 10000) + 1,
    customerId: 1, // Assuming current user
    items: await Promise.all(
      orderData.items.map(async (item) => {
        const product = await fetchProductById(item.productId)
        return {
          productId: item.productId,
          product: product!,
          quantity: item.quantity,
        }
      }),
    ),
    totalAmount: 0, // Will be calculated below
    status: "pending",
    createdAt: new Date().toISOString(),
    updatedAt: new Date().toISOString(),
    shippingAddress: orderData.shippingAddress,
    paymentMethod: orderData.paymentMethod,
  }

  // Calculate total amount
  mockOrder.totalAmount = mockOrder.items.reduce((total, item) => total + item.product.price * item.quantity, 0)

  return mockOrder
}

// Fetch orders for current user
export async function fetchUserOrders(): Promise<Order[]> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Generate some mock products first
  const mockProducts = await fetchProducts()
  const products = mockProducts.products.slice(0, 5)

  // Mock orders data
  const mockOrders: Order[] = Array.from({ length: 3 }, (_, i) => {
    // Create 1-3 items per order
    const itemCount = Math.floor(Math.random() * 3) + 1
    const orderItems = Array.from({ length: itemCount }, (_, j) => {
      const product = products[Math.floor(Math.random() * products.length)]
      return {
        productId: product.id,
        product,
        quantity: Math.floor(Math.random() * 3) + 1,
      }
    })

    // Calculate total amount
    const totalAmount = orderItems.reduce((total, item) => total + item.product.price * item.quantity, 0)

    // Generate random date within the last 30 days
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 30))

    return {
      id: i + 1,
      customerId: 1, // Assuming current user
      items: orderItems,
      totalAmount,
      status: ["pending", "processing", "shipped", "delivered"][Math.floor(Math.random() * 4)] as any,
      createdAt: date.toISOString(),
      updatedAt: date.toISOString(),
      shippingAddress: "123 Customer St, City, Country",
      paymentMethod: ["Credit Card", "PayPal", "Cash on Delivery"][Math.floor(Math.random() * 3)],
    }
  })

  return mockOrders
}

// Fetch a single order by ID
export async function fetchOrderById(id: number): Promise<Order | null> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Fetch all orders and find the one with matching ID
  const orders = await fetchUserOrders()
  return orders.find((order) => order.id === id) || null
}

// SELLER API FUNCTIONS

// Fetch products for the current seller
export async function fetchSellerProducts(
  options: {
    category?: string
    search?: string
    sortBy?: string
    page?: number
    limit?: number
  } = {},
): Promise<{ products: Product[]; total: number }> {
  // In a real app, this would call your PHP backend with the seller's ID
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Mock seller products data
  const mockProducts: Product[] = Array.from({ length: 15 }, (_, i) => ({
    id: i + 1,
    name: `Seller Product ${i + 1}`,
    description: `This is a description for seller product ${i + 1}. It contains all the details about the product.`,
    price: Math.floor(Math.random() * 10000) / 100 + 100,
    imageUrl: `/placeholder.svg?height=200&width=200&text=Product+${i + 1}`,
    category: ["Electronics", "Clothing", "Home & Kitchen", "Beauty", "Books"][Math.floor(Math.random() * 5)],
    sellerId: 2, // Assuming seller ID is 2
    sellerName: "Your Store",
    rating: Math.floor(Math.random() * 50) / 10 + 1,
    reviewCount: Math.floor(Math.random() * 100) + 1,
    stock: Math.floor(Math.random() * 100) + 1,
  }))

  // Apply filters
  let filteredProducts = [...mockProducts]

  if (options.category) {
    filteredProducts = filteredProducts.filter((p) => p.category === options.category)
  }

  if (options.search) {
    const searchLower = options.search.toLowerCase()
    filteredProducts = filteredProducts.filter(
      (p) => p.name.toLowerCase().includes(searchLower) || p.description.toLowerCase().includes(searchLower),
    )
  }

  // Apply sorting
  if (options.sortBy) {
    switch (options.sortBy) {
      case "price-asc":
        filteredProducts.sort((a, b) => a.price - b.price)
        break
      case "price-desc":
        filteredProducts.sort((a, b) => b.price - a.price)
        break
      case "rating":
        filteredProducts.sort((a, b) => b.rating - a.rating)
        break
      case "newest":
        filteredProducts.sort((a, b) => b.id - a.id)
        break
    }
  }

  // Apply pagination
  const page = options.page || 1
  const limit = options.limit || 10
  const startIndex = (page - 1) * limit
  const endIndex = startIndex + limit
  const paginatedProducts = filteredProducts.slice(startIndex, endIndex)

  return {
    products: paginatedProducts,
    total: filteredProducts.length,
  }
}

// Create a new product
export async function createProduct(
  productData: Omit<Product, "id" | "sellerId" | "sellerName" | "rating" | "reviewCount">,
): Promise<Product> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll simulate creating a product

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  // Create a new product with mock data
  const newProduct: Product = {
    id: Math.floor(Math.random() * 1000) + 100,
    sellerId: 2, // Assuming seller ID is 2
    sellerName: "Your Store",
    rating: 0,
    reviewCount: 0,
    ...productData,
  }

  return newProduct
}

// Update an existing product
export async function updateProduct(id: number, productData: Partial<Product>): Promise<Product> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll simulate updating a product

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  // Get the existing product
  const existingProduct = await fetchProductById(id)

  if (!existingProduct) {
    throw new Error("Product not found")
  }

  // Update the product
  const updatedProduct: Product = {
    ...existingProduct,
    ...productData,
  }

  return updatedProduct
}

// Delete a product
export async function deleteProduct(id: number): Promise<boolean> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll simulate deleting a product

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  // Return success
  return true
}

// Fetch orders for seller's products
export async function fetchSellerOrders(
  options: {
    status?: string
    search?: string
    sortBy?: string
    page?: number
    limit?: number
  } = {},
): Promise<{ orders: Order[]; total: number }> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Get seller products
  const sellerProductsResult = await fetchSellerProducts({ limit: 100 })
  const sellerProducts = sellerProductsResult.products

  // Generate mock orders
  const mockOrders: Order[] = Array.from({ length: 20 }, (_, i) => {
    // Create 1-3 items per order
    const itemCount = Math.floor(Math.random() * 2) + 1
    const orderItems = Array.from({ length: itemCount }, (_, j) => {
      const product = sellerProducts[Math.floor(Math.random() * sellerProducts.length)]
      return {
        productId: product.id,
        product,
        quantity: Math.floor(Math.random() * 3) + 1,
      }
    })

    // Calculate total amount
    const totalAmount = orderItems.reduce((total, item) => total + item.product.price * item.quantity, 0)

    // Generate random date within the last 30 days
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 30))

    // Random customer ID
    const customerId = Math.floor(Math.random() * 100) + 1

    return {
      id: i + 100,
      customerId,
      customerName: `Customer ${customerId}`,
      customerEmail: `customer${customerId}@example.com`,
      items: orderItems,
      totalAmount,
      status: ["pending", "processing", "shipped", "delivered", "cancelled"][Math.floor(Math.random() * 5)] as any,
      createdAt: date.toISOString(),
      updatedAt: date.toISOString(),
      shippingAddress: `${Math.floor(Math.random() * 1000) + 1} Customer St, City, Country`,
      paymentMethod: ["Credit Card", "PayPal", "Cash on Delivery"][Math.floor(Math.random() * 3)],
    }
  })

  // Apply filters
  let filteredOrders = [...mockOrders]

  if (options.status) {
    filteredOrders = filteredOrders.filter((o) => o.status === options.status)
  }

  if (options.search) {
    const searchLower = options.search.toLowerCase()
    filteredOrders = filteredOrders.filter(
      (o) =>
        o.customerName?.toLowerCase().includes(searchLower) ||
        o.customerEmail?.toLowerCase().includes(searchLower) ||
        o.id.toString().includes(searchLower),
    )
  }

  // Apply sorting
  if (options.sortBy) {
    switch (options.sortBy) {
      case "date-asc":
        filteredOrders.sort((a, b) => new Date(a.createdAt).getTime() - new Date(b.createdAt).getTime())
        break
      case "date-desc":
        filteredOrders.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
        break
      case "amount-asc":
        filteredOrders.sort((a, b) => a.totalAmount - b.totalAmount)
        break
      case "amount-desc":
        filteredOrders.sort((a, b) => b.totalAmount - a.totalAmount)
        break
    }
  } else {
    // Default sort by date descending
    filteredOrders.sort((a, b) => new Date(b.createdAt).getTime() - new Date(a.createdAt).getTime())
  }

  // Apply pagination
  const page = options.page || 1
  const limit = options.limit || 10
  const startIndex = (page - 1) * limit
  const endIndex = startIndex + limit
  const paginatedOrders = filteredOrders.slice(startIndex, endIndex)

  return {
    orders: paginatedOrders,
    total: filteredOrders.length,
  }
}

// Update order status
export async function updateOrderStatus(orderId: number, status: string): Promise<Order> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll simulate updating an order

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  // Get the order
  const { orders } = await fetchSellerOrders()
  const order = orders.find((o) => o.id === orderId)

  if (!order) {
    throw new Error("Order not found")
  }

  // Update the order
  const updatedOrder: Order = {
    ...order,
    status: status as any,
    updatedAt: new Date().toISOString(),
  }

  return updatedOrder
}

// Fetch product categories
export async function fetchProductCategories(): Promise<ProductCategory[]> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 300))

  // Mock categories
  const mockCategories: ProductCategory[] = [
    { id: 1, name: "Electronics", description: "Electronic devices and accessories" },
    { id: 2, name: "Clothing", description: "Apparel and fashion items" },
    { id: 3, name: "Home & Kitchen", description: "Home goods and kitchen appliances" },
    { id: 4, name: "Beauty", description: "Beauty and personal care products" },
    { id: 5, name: "Books", description: "Books, e-books, and publications" },
    { id: 6, name: "Toys & Games", description: "Toys, games, and entertainment items" },
    { id: 7, name: "Sports & Outdoors", description: "Sports equipment and outdoor gear" },
    { id: 8, name: "Health & Wellness", description: "Health supplements and wellness products" },
    { id: 9, name: "Automotive", description: "Car parts and accessories" },
    { id: 10, name: "Pet Supplies", description: "Products for pets and animals" },
  ]

  return mockCategories
}

// Fetch reviews for a product
export async function fetchProductReviews(productId: number): Promise<Review[]> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 500))

  // Mock reviews
  const mockReviews: Review[] = Array.from({ length: Math.floor(Math.random() * 5) + 3 }, (_, i) => {
    const date = new Date()
    date.setDate(date.getDate() - Math.floor(Math.random() * 60))

    return {
      id: i + 1,
      productId,
      customerId: Math.floor(Math.random() * 100) + 1,
      customerName: `Customer ${Math.floor(Math.random() * 100) + 1}`,
      rating: Math.floor(Math.random() * 5) + 1,
      title: [
        "Great product!",
        "Satisfied with my purchase",
        "Could be better",
        "Excellent quality",
        "Not what I expected",
      ][Math.floor(Math.random() * 5)],
      comment: `This is a review for product ${productId}. ${["I really like this product.", "The quality is excellent.", "It works as expected.", "I would recommend this to others.", "It could be improved in some areas."][Math.floor(Math.random() * 5)]}`,
      createdAt: date.toISOString(),
      sellerResponse:
        Math.random() > 0.5
          ? {
              comment: `Thank you for your feedback! ${["We appreciate your review.", "We're glad you enjoyed the product.", "We'll take your suggestions into consideration."][Math.floor(Math.random() * 3)]}`,
              createdAt: new Date(date.getTime() + 86400000 * Math.floor(Math.random() * 5)).toISOString(),
            }
          : undefined,
    }
  })

  return mockReviews
}

// Add a seller response to a review
export async function addReviewResponse(reviewId: number, comment: string): Promise<Review> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll simulate adding a response

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 1000))

  // Mock updated review
  const mockReview: Review = {
    id: reviewId,
    productId: Math.floor(Math.random() * 100) + 1,
    customerId: Math.floor(Math.random() * 100) + 1,
    customerName: `Customer ${Math.floor(Math.random() * 100) + 1}`,
    rating: Math.floor(Math.random() * 5) + 1,
    title: "Great product!",
    comment: "This is a review comment.",
    createdAt: new Date(Date.now() - 86400000 * 5).toISOString(),
    sellerResponse: {
      comment,
      createdAt: new Date().toISOString(),
    },
  }

  return mockReview
}

// Fetch seller statistics
export async function fetchSellerStats(): Promise<SellerStats> {
  // In a real app, this would call your PHP backend
  // For demo purposes, we'll return mock data

  // Simulate API call delay
  await new Promise((resolve) => setTimeout(resolve, 800))

  // Get seller products
  const sellerProductsResult = await fetchSellerProducts({ limit: 100 })
  const sellerProducts = sellerProductsResult.products

  // Calculate total revenue
  const totalRevenue = sellerProducts.reduce((total, product) => {
    // Simulate some sales for each product
    const sales = Math.floor(Math.random() * 20) + 1
    return total + product.price * sales
  }, 0)

  // Generate top selling products
  const topSellingProducts = [...sellerProducts]
    .sort(() => Math.random() - 0.5)
    .slice(0, 5)
    .map((product) => ({
      product,
      salesCount: Math.floor(Math.random() * 50) + 10,
      revenue: product.price * (Math.floor(Math.random() * 50) + 10),
    }))

  // Generate sales by category
  const salesByCategory = [
    { category: "Electronics", sales: Math.floor(Math.random() * 5000) + 1000 },
    { category: "Clothing", sales: Math.floor(Math.random() * 5000) + 1000 },
    { category: "Home & Kitchen", sales: Math.floor(Math.random() * 5000) + 1000 },
    { category: "Beauty", sales: Math.floor(Math.random() * 5000) + 1000 },
    { category: "Books", sales: Math.floor(Math.random() * 5000) + 1000 },
  ]

  // Generate monthly sales data
  const monthlySales = Array.from({ length: 6 }, (_, i) => {
    const date = new Date()
    date.setMonth(date.getMonth() - i)

    return {
      month: date.toLocaleString("default", { month: "long" }),
      year: date.getFullYear(),
      sales: Math.floor(Math.random() * 10000) + 5000,
    }
  }).reverse()

  // Mock stats
  const mockStats: SellerStats = {
    totalProducts: sellerProducts.length,
    totalOrders: Math.floor(Math.random() * 100) + 50,
    totalRevenue,
    averageRating: Number.parseFloat((Math.random() * 2 + 3).toFixed(1)),
    topSellingProducts,
    salesByCategory,
    monthlySales,
    pendingOrders: Math.floor(Math.random() * 10) + 5,
    lowStockProducts: Math.floor(Math.random() * 5) + 2,
  }

  return mockStats
}
