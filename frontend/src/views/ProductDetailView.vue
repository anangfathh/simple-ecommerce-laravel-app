<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/products'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'
import Card from '@/components/ui/Card.vue'
import CardContent from '@/components/ui/CardContent.vue'
import { ChevronLeft, Package, Tag, Calendar } from 'lucide-vue-next'

const route = useRoute()
const productStore = useProductStore()
const loading = ref(true)

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
  }).format(price)
}

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  })
}

onMounted(async () => {
  const productId = Number(route.params.id)
  await productStore.fetchProduct(productId)
  
  // Fetch related products
  if (productStore.currentProduct?.category_id) {
    await productStore.fetchProducts({
      category: productStore.currentProduct.category_id,
      per_page: 4,
    })
  }
  
  loading.value = false
})
</script>

<template>
  <div class="min-h-screen bg-muted/20">
    <div class="container max-w-7xl py-8">
      <!-- Back Link -->
      <RouterLink to="/products" class="inline-flex items-center text-sm text-muted-foreground hover:text-primary transition-colors mb-8">
        <ChevronLeft class="h-4 w-4 mr-1" />
        Back to products
      </RouterLink>

      <!-- Loading State -->
      <div v-if="loading" class="animate-pulse">
        <div class="grid md:grid-cols-2 gap-8">
          <div class="aspect-square bg-muted rounded-xl"></div>
          <div class="space-y-4">
            <div class="h-8 bg-muted rounded w-3/4"></div>
            <div class="h-6 bg-muted rounded w-1/4"></div>
            <div class="h-24 bg-muted rounded"></div>
          </div>
        </div>
      </div>

      <!-- Product Not Found -->
      <div v-else-if="!productStore.currentProduct" class="text-center py-16">
        <Package class="h-16 w-16 mx-auto text-muted-foreground mb-4" />
        <h2 class="text-2xl font-semibold mb-2">Product Not Found</h2>
        <p class="text-muted-foreground mb-6">The product you're looking for doesn't exist.</p>
        <RouterLink to="/products">
          <Button>Browse Products</Button>
        </RouterLink>
      </div>

      <!-- Product Details -->
      <div v-else class="grid md:grid-cols-2 gap-12">
        <!-- Product Image -->
        <div class="space-y-4">
          <div class="aspect-square bg-card rounded-xl overflow-hidden border border-border shadow-lg">
            <img
              v-if="productStore.currentProduct.image"
              :src="`http://localhost:8000/storage/${productStore.currentProduct.image}`"
              :alt="productStore.currentProduct.name"
              class="w-full h-full object-cover"
            />
            <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10">
              <span class="text-8xl text-muted-foreground/30">{{ productStore.currentProduct.name.charAt(0) }}</span>
            </div>
          </div>
        </div>

        <!-- Product Info -->
        <div class="space-y-6">
          <div>
            <Badge v-if="productStore.currentProduct.category" variant="secondary" class="mb-3">
              <Tag class="h-3 w-3 mr-1" />
              {{ productStore.currentProduct.category.name }}
            </Badge>
            <h1 class="text-3xl md:text-4xl font-bold">{{ productStore.currentProduct.name }}</h1>
          </div>

          <div class="text-4xl font-bold text-primary">
            {{ formatPrice(productStore.currentProduct.price) }}
          </div>

          <p class="text-muted-foreground leading-relaxed text-lg">
            {{ productStore.currentProduct.description || 'No description available.' }}
          </p>

          <!-- Product Meta -->
          <div class="flex items-center gap-4 text-sm text-muted-foreground pt-6 border-t border-border">
            <div class="flex items-center">
              <Calendar class="h-4 w-4 mr-1" />
              Added {{ formatDate(productStore.currentProduct.created_at) }}
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div v-if="productStore.currentProduct && productStore.products.length > 1" class="mt-16">
        <h2 class="text-2xl font-bold mb-6">Related Products</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <RouterLink
            v-for="product in productStore.products.filter(p => p.id !== productStore.currentProduct?.id).slice(0, 4)"
            :key="product.id"
            :to="`/products/${product.id}`"
            class="group"
          >
            <Card class="hover-lift overflow-hidden">
              <div class="aspect-square bg-muted relative overflow-hidden">
                <img
                  v-if="product.image"
                  :src="`http://localhost:8000/storage/${product.image}`"
                  :alt="product.name"
                  class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                />
                <div v-else class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary/5 to-primary/10">
                  <span class="text-4xl text-muted-foreground/50">{{ product.name.charAt(0) }}</span>
                </div>
              </div>
              <CardContent class="pt-4">
                <h3 class="font-semibold truncate group-hover:text-primary transition-colors">
                  {{ product.name }}
                </h3>
                <span class="text-lg font-bold text-primary">
                  {{ formatPrice(product.price) }}
                </span>
              </CardContent>
            </Card>
          </RouterLink>
        </div>
      </div>
    </div>
  </div>
</template>
