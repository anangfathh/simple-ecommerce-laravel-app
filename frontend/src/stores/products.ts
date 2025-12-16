import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/services/api'

export interface Category {
  id: number
  name: string
  slug: string
  description: string | null
  products_count?: number
  created_at: string
  updated_at: string
}

export interface Product {
  id: number
  name: string
  description: string | null
  price: number
  category_id: number
  image: string | null
  category?: Category
  created_at: string
  updated_at: string
}

export interface ProductFilters {
  category?: number
  min_price?: number
  max_price?: number
  search?: string
  per_page?: number
  page?: number
}

export const useProductStore = defineStore('products', () => {
  const products = ref<Product[]>([])
  const categories = ref<Category[]>([])
  const currentProduct = ref<Product | null>(null)
  const loading = ref(false)
  const error = ref<string | null>(null)
  const pagination = ref({
    currentPage: 1,
    lastPage: 1,
    perPage: 12,
    total: 0,
  })

  const fetchProducts = async (filters: ProductFilters = {}) => {
    loading.value = true
    error.value = null
    
    try {
      const params = new URLSearchParams()
      if (filters.category) params.append('category', filters.category.toString())
      if (filters.min_price) params.append('min_price', filters.min_price.toString())
      if (filters.max_price) params.append('max_price', filters.max_price.toString())
      if (filters.search) params.append('search', filters.search)
      params.append('per_page', (filters.per_page || 12).toString())
      if (filters.page) params.append('page', filters.page.toString())
      
      const response = await api.get(`/products?${params.toString()}`)
      
      products.value = response.data.data
      pagination.value = {
        currentPage: response.data.current_page,
        lastPage: response.data.last_page,
        perPage: response.data.per_page,
        total: response.data.total,
      }
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch products'
    } finally {
      loading.value = false
    }
  }

  const fetchProduct = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.get(`/products/${id}`)
      currentProduct.value = response.data
      return response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch product'
      return null
    } finally {
      loading.value = false
    }
  }

  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories')
      categories.value = response.data
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to fetch categories'
    }
  }

  const createProduct = async (productData: FormData) => {
    loading.value = true
    error.value = null
    
    try {
      const response = await api.post('/products', productData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      })
      products.value.unshift(response.data.product)
      return response.data.product
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create product'
      throw err
    } finally {
      loading.value = false
    }
  }

  const updateProduct = async (id: number, productData: FormData | object) => {
    loading.value = true
    error.value = null
    
    try {
      let response
      
      if (productData instanceof FormData) {
        // FormData already contains _method: 'PUT' from the form
        // Use POST method because browsers can't send files via PUT
        // Laravel will interpret _method=PUT and route to update handler
        response = await api.post(`/products/${id}`, productData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })
      } else {
        // For regular JSON data, use PUT directly
        response = await api.put(`/products/${id}`, productData)
      }
      
      const index = products.value.findIndex(p => p.id === id)
      if (index !== -1) {
        products.value[index] = response.data.product
      }
      
      return response.data.product
    } catch (err: any) {
      console.error('Update product error:', err.response?.data || err)
      error.value = err.response?.data?.message || 'Failed to update product'
      throw err
    } finally {
      loading.value = false
    }
  }

  const deleteProduct = async (id: number) => {
    loading.value = true
    error.value = null
    
    try {
      await api.delete(`/products/${id}`)
      products.value = products.value.filter(p => p.id !== id)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete product'
      return false
    } finally {
      loading.value = false
    }
  }

  const createCategory = async (categoryData: { name: string; description?: string }) => {
    try {
      const response = await api.post('/categories', categoryData)
      categories.value.push(response.data.category)
      return response.data.category
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to create category'
      throw err
    }
  }

  const deleteCategory = async (id: number) => {
    try {
      await api.delete(`/categories/${id}`)
      categories.value = categories.value.filter(c => c.id !== id)
      return true
    } catch (err: any) {
      error.value = err.response?.data?.message || 'Failed to delete category'
      return false
    }
  }

  return {
    products,
    categories,
    currentProduct,
    loading,
    error,
    pagination,
    fetchProducts,
    fetchProduct,
    fetchCategories,
    createProduct,
    updateProduct,
    deleteProduct,
    createCategory,
    deleteCategory,
  }
})
