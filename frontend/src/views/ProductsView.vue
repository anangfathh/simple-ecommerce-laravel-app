<script setup lang="ts">
import { ref, onMounted, watch } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import { useProductStore, type ProductFilters } from '@/stores/products'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Select from '@/components/ui/Select.vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Badge from '@/components/ui/Badge.vue'
import { Search, Filter, X, ChevronLeft, ChevronRight, Grid, List } from 'lucide-vue-next'

const route = useRoute()
const productStore = useProductStore()

const filters = ref<ProductFilters>({
  search: '',
  category: undefined,
  min_price: undefined,
  max_price: undefined,
  page: 1,
  per_page: 12,
})

const showFilters = ref(false)
const viewMode = ref<'grid' | 'list'>('grid')

const categoryOptions = ref<{ value: string | number; label: string }[]>([])

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(price)
}

const loadProducts = async () => {
  const cleanFilters: ProductFilters = { ...filters.value }
  
  // Remove empty values
  if (!cleanFilters.search) delete cleanFilters.search
  if (!cleanFilters.category) delete cleanFilters.category
  if (!cleanFilters.min_price) delete cleanFilters.min_price
  if (!cleanFilters.max_price) delete cleanFilters.max_price
  
  await productStore.fetchProducts(cleanFilters)
}

const handleSearch = () => {
  filters.value.page = 1
  loadProducts()
}

const handleCategoryChange = (value: string | number) => {
  filters.value.category = value ? Number(value) : undefined
  filters.value.page = 1
  loadProducts()
}

const clearFilters = () => {
  filters.value = {
    search: '',
    category: undefined,
    min_price: undefined,
    max_price: undefined,
    page: 1,
    per_page: 12,
  }
  loadProducts()
}

const goToPage = (page: number) => {
  filters.value.page = page
  loadProducts()
}

onMounted(async () => {
  await productStore.fetchCategories()
  
  // Build category options
  categoryOptions.value = [
    { value: '', label: 'All Categories' },
    ...productStore.categories.map(c => ({
      value: c.id,
      label: c.name,
    })),
  ]
  
  // Check for category from URL
  if (route.query.category) {
    filters.value.category = Number(route.query.category)
  }
  
  await loadProducts()
})

watch(() => route.query, (query) => {
  if (query.category) {
    filters.value.category = Number(query.category)
    loadProducts()
  }
}, { deep: true })
</script>

<template>
  <div class="min-h-screen bg-muted/20">
    <!-- Page Header -->
    <div class="bg-gradient-to-r from-violet-600/10 via-purple-600/5 to-indigo-600/10 py-12">
      <div class="container max-w-7xl">
        <h1 class="text-3xl md:text-4xl font-bold">All Products</h1>
        <p class="text-muted-foreground mt-2">
          Browse our collection of {{ productStore.pagination.total }} products
        </p>
      </div>
    </div>

    <div class="container max-w-7xl py-8">
      <!-- Filters Bar -->
      <div class="flex flex-col md:flex-row gap-4 mb-8">
        <!-- Search -->
        <div class="relative flex-1">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
          <Input
            v-model="filters.search"
            placeholder="Search products..."
            class="pl-10"
            @keyup.enter="handleSearch"
          />
        </div>
        
        <!-- Category Filter -->
        <div class="w-full md:w-56">
          <Select
            :model-value="filters.category?.toString() || ''"
            :options="categoryOptions"
            placeholder="All Categories"
            @update:model-value="handleCategoryChange"
          />
        </div>

        <!-- Filter Toggle (Mobile) -->
        <Button variant="outline" class="md:hidden" @click="showFilters = !showFilters">
          <Filter class="h-4 w-4 mr-2" />
          Filters
        </Button>

        <!-- View Toggle -->
        <div class="hidden md:flex items-center space-x-1">
          <Button
            variant="ghost"
            size="icon"
            :class="viewMode === 'grid' ? 'bg-muted' : ''"
            @click="viewMode = 'grid'"
          >
            <Grid class="h-4 w-4" />
          </Button>
          <Button
            variant="ghost"
            size="icon"
            :class="viewMode === 'list' ? 'bg-muted' : ''"
            @click="viewMode = 'list'"
          >
            <List class="h-4 w-4" />
          </Button>
        </div>

        <!-- Search Button -->
        <Button @click="handleSearch">
          <Search class="h-4 w-4 mr-2" />
          Search
        </Button>
      </div>

      <!-- Extended Filters (Mobile) -->
      <div v-if="showFilters" class="md:hidden mb-8 p-4 bg-card rounded-xl border border-border">
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm font-medium mb-1 block">Min Price</label>
              <Input
                v-model.number="filters.min_price"
                type="number"
                placeholder="$0"
              />
            </div>
            <div>
              <label class="text-sm font-medium mb-1 block">Max Price</label>
              <Input
                v-model.number="filters.max_price"
                type="number"
                placeholder="$1000"
              />
            </div>
          </div>
          <div class="flex gap-2">
            <Button variant="outline" class="flex-1" @click="clearFilters">
              Clear
            </Button>
            <Button class="flex-1" @click="handleSearch; showFilters = false">
              Apply
            </Button>
          </div>
        </div>
      </div>

      <!-- Active Filters -->
      <div v-if="filters.category || filters.min_price || filters.max_price || filters.search" class="flex flex-wrap gap-2 mb-6">
        <Badge v-if="filters.search" variant="secondary" class="px-3 py-1">
          Search: {{ filters.search }}
          <button class="ml-2" @click="filters.search = ''; handleSearch()">
            <X class="h-3 w-3" />
          </button>
        </Badge>
        <Badge v-if="filters.category" variant="secondary" class="px-3 py-1">
          Category: {{ productStore.categories.find(c => c.id === filters.category)?.name }}
          <button class="ml-2" @click="filters.category = undefined; handleSearch()">
            <X class="h-3 w-3" />
          </button>
        </Badge>
        <Badge v-if="filters.min_price" variant="secondary" class="px-3 py-1">
          Min: {{ formatPrice(filters.min_price) }}
          <button class="ml-2" @click="filters.min_price = undefined; handleSearch()">
            <X class="h-3 w-3" />
          </button>
        </Badge>
        <Badge v-if="filters.max_price" variant="secondary" class="px-3 py-1">
          Max: {{ formatPrice(filters.max_price) }}
          <button class="ml-2" @click="filters.max_price = undefined; handleSearch()">
            <X class="h-3 w-3" />
          </button>
        </Badge>
        <Button variant="ghost" size="sm" @click="clearFilters">
          Clear All
        </Button>
      </div>

      <!-- Loading State -->
      <div v-if="productStore.loading" :class="viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4'">
        <div v-for="i in 8" :key="i" class="animate-pulse">
          <Card>
            <div :class="viewMode === 'grid' ? 'aspect-square' : 'h-32'" class="bg-muted"></div>
            <CardContent class="space-y-2 pt-4">
              <div class="h-4 bg-muted rounded w-3/4"></div>
              <div class="h-4 bg-muted rounded w-1/2"></div>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Products Grid -->
      <div v-else-if="productStore.products.length > 0" :class="viewMode === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4'">
        <RouterLink
          v-for="product in productStore.products"
          :key="product.id"
          :to="`/products/${product.id}`"
          class="group"
        >
          <Card :class="viewMode === 'list' ? 'flex flex-row' : ''" class="hover-lift overflow-hidden">
            <div :class="viewMode === 'grid' ? 'aspect-square' : 'w-32 h-32 flex-shrink-0'" class="bg-muted relative overflow-hidden">
              <img
                v-if="product.image"
                :src="`http://localhost:8000/storage/${product.image}`"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
              />
              <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10">
                <span class="text-4xl text-muted-foreground/50">{{ product.name.charAt(0) }}</span>
              </div>
              <Badge
                v-if="product.category && viewMode === 'grid'"
                variant="secondary"
                class="absolute top-3 left-3"
              >
                {{ product.category.name }}
              </Badge>
            </div>
            <CardContent :class="viewMode === 'list' ? 'flex flex-col justify-center flex-1' : ''" class="pt-4">
              <div v-if="product.category && viewMode === 'list'" class="mb-1">
                <Badge variant="secondary" class="text-xs">{{ product.category.name }}</Badge>
              </div>
              <h3 class="font-semibold truncate group-hover:text-primary transition-colors">
                {{ product.name }}
              </h3>
              <p v-if="viewMode === 'list'" class="text-sm text-muted-foreground line-clamp-2 mt-1">
                {{ product.description }}
              </p>
              <div class="mt-2">
                <span class="text-lg font-bold text-primary">
                  {{ formatPrice(product.price) }}
                </span>
              </div>
            </CardContent>
          </Card>
        </RouterLink>
      </div>

      <!-- Empty State -->
      <div v-else class="text-center py-16">
        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-muted flex items-center justify-center">
          <Search class="h-12 w-12 text-muted-foreground" />
        </div>
        <h3 class="text-xl font-semibold mb-2">No products found</h3>
        <p class="text-muted-foreground mb-6">
          Try adjusting your search or filter criteria
        </p>
        <Button variant="outline" @click="clearFilters">Clear Filters</Button>
      </div>

      <!-- Pagination -->
      <div v-if="productStore.pagination.lastPage > 1" class="mt-8 flex items-center justify-center space-x-2">
        <Button
          variant="outline"
          size="icon"
          :disabled="productStore.pagination.currentPage === 1"
          @click="goToPage(productStore.pagination.currentPage - 1)"
        >
          <ChevronLeft class="h-4 w-4" />
        </Button>
        
        <template v-for="page in productStore.pagination.lastPage" :key="page">
          <Button
            v-if="page === 1 || page === productStore.pagination.lastPage || (page >= productStore.pagination.currentPage - 2 && page <= productStore.pagination.currentPage + 2)"
            :variant="page === productStore.pagination.currentPage ? 'default' : 'outline'"
            size="icon"
            @click="goToPage(page)"
          >
            {{ page }}
          </Button>
          <span
            v-else-if="page === productStore.pagination.currentPage - 3 || page === productStore.pagination.currentPage + 3"
            class="px-2"
          >
            ...
          </span>
        </template>

        <Button
          variant="outline"
          size="icon"
          :disabled="productStore.pagination.currentPage === productStore.pagination.lastPage"
          @click="goToPage(productStore.pagination.currentPage + 1)"
        >
          <ChevronRight class="h-4 w-4" />
        </Button>
      </div>
    </div>
  </div>
</template>
