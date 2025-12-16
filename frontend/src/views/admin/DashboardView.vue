<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/stores/products'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import { Package, Tags, DollarSign, TrendingUp } from 'lucide-vue-next'

const productStore = useProductStore()

const stats = ref([
  { title: 'Total Products', value: '0', icon: Package, color: 'text-blue-500', bg: 'bg-blue-500/10' },
  { title: 'Categories', value: '0', icon: Tags, color: 'text-green-500', bg: 'bg-green-500/10' },
  { title: 'Avg. Price', value: '$0', icon: DollarSign, color: 'text-purple-500', bg: 'bg-purple-500/10' },
  { title: 'This Week', value: '+12%', icon: TrendingUp, color: 'text-orange-500', bg: 'bg-orange-500/10' },
])

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(price)
}

onMounted(async () => {
  await productStore.fetchProducts({ per_page: 100 })
  await productStore.fetchCategories()
  
  // Calculate stats
  stats.value[0]!.value = productStore.pagination.total.toString()
  stats.value[1]!.value = productStore.categories.length.toString()
  
  if (productStore.products.length > 0) {
    const avgPrice = productStore.products.reduce((sum, p) => sum + Number(p.price), 0) / productStore.products.length
    stats.value[2]!.value = formatPrice(avgPrice)
  }
})
</script>

<template>
  <div class="space-y-8">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold">Dashboard</h1>
      <p class="text-muted-foreground mt-1">Welcome back! Here's an overview of your store.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <Card v-for="stat in stats" :key="stat.title" class="hover-lift">
        <CardHeader class="flex flex-row items-center justify-between pb-2">
          <CardTitle class="text-sm font-medium text-muted-foreground">
            {{ stat.title }}
          </CardTitle>
          <div :class="[stat.bg, 'p-2 rounded-lg']">
            <component :is="stat.icon" :class="[stat.color, 'h-5 w-5']" />
          </div>
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ stat.value }}</div>
        </CardContent>
      </Card>
    </div>

    <!-- Recent Products -->
    <div class="grid lg:grid-cols-2 gap-6">
      <Card>
        <CardHeader>
          <CardTitle>Recent Products</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div
              v-for="product in productStore.products.slice(0, 5)"
              :key="product.id"
              class="flex items-center space-x-4"
            >
              <div class="w-12 h-12 rounded-lg bg-muted flex items-center justify-center overflow-hidden">
                <img
                  v-if="product.image"
                  :src="`http://localhost:8000/storage/${product.image}`"
                  :alt="product.name"
                  class="w-full h-full object-cover"
                />
                <Package v-else class="h-6 w-6 text-muted-foreground" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="font-medium truncate">{{ product.name }}</p>
                <p class="text-sm text-muted-foreground">{{ product.category?.name }}</p>
              </div>
              <div class="text-right">
                <p class="font-medium text-primary">${{ product.price }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle>Categories</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="space-y-4">
            <div
              v-for="category in productStore.categories"
              :key="category.id"
              class="flex items-center justify-between p-3 rounded-lg bg-muted/50 hover:bg-muted transition-colors"
            >
              <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
                  <Tags class="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p class="font-medium">{{ category.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ category.products_count || 0 }} products</p>
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>
    </div>
  </div>
</template>
