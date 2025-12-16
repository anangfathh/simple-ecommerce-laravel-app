<script setup lang="ts">
import { onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/products'
import Button from '@/components/ui/Button.vue'
import Badge from '@/components/ui/Badge.vue'
import { ArrowRight, Sparkles, ShieldCheck, Truck, Trophy } from 'lucide-vue-next'

const productStore = useProductStore()

onMounted(async () => {
  await productStore.fetchProducts({ per_page: 8 })
  await productStore.fetchCategories()
})

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(price)
}
</script>

<template>
  <div class="overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative bg-background border-b border-border/40">
      <div class="container max-w-7xl px-4 py-12 md:py-24 lg:py-32">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
          <div class="space-y-8 animate-fade-in text-center lg:text-left">
            <div class="inline-flex items-center rounded-full border border-primary/10 bg-primary/5 px-3 py-1 text-sm font-medium text-primary backdrop-blur-sm">
              <Sparkles class="mr-2 h-3.5 w-3.5 fill-primary" />
              New Collection Available
            </div>
            
            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-bold tracking-tight text-balance leading-[1.1]">
              Elevate Your <br />
              <span class="gradient-text">Lifestyle Today</span>
            </h1>
            
            <p class="text-lg md:text-xl text-muted-foreground text-balance max-w-2xl mx-auto lg:mx-0">
              Discover a curated collection of premium products designed for the modern individual. 
              Quality visuals, exceptional craft, and timeless style.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
              <RouterLink to="/products">
                <Button size="lg" class="w-full sm:w-auto px-8 h-12 text-base rounded-full shadow-lg shadow-primary/20 hover:shadow-primary/30 transition-shadow">
                  Shop Collection
                  <ArrowRight class="ml-2 h-4 w-4" />
                </Button>
              </RouterLink>
            </div>

            <div class="pt-8 flex items-center justify-center lg:justify-start gap-8 text-sm text-muted-foreground">
              <div class="flex items-center gap-2">
                <ShieldCheck class="h-4 w-4" />
                <span>Authentic Guarantee</span>
              </div>
              <div class="flex items-center gap-2">
                <Truck class="h-4 w-4" />
                <span>Free Shipping</span>
              </div>
            </div>
          </div>

          <div class="relative lg:h-[600px] w-full animate-slide-in-from-bottom lg:animate-slide-in-from-top delayed-200">
            <div class="relative h-full w-full rounded-2xl overflow-hidden shadow-2xl bg-muted aspect-[4/5] lg:aspect-auto">
              <img 
                src="https://images.unsplash.com/photo-1483985988355-763728e1935b?q=80&w=2070&auto=format&fit=crop" 
                alt="Hero Image" 
                class="object-cover w-full h-full hover:scale-105 transition-transform duration-700"
              />
              <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent"></div>
            </div>
            
            <!-- Floating Card -->
            <div class="absolute -bottom-6 -left-6 md:bottom-12 md:-left-12 bg-white dark:bg-zinc-900 p-4 rounded-xl shadow-xl border border-border/50 max-w-[240px] hidden md:block animate-bounce-slow">
              <div class="flex items-center gap-3 mb-2">
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center">
                  <Trophy class="h-5 w-5 text-primary" />
                </div>
                <div>
                  <p class="font-bold text-sm">Best Seller</p>
                  <p class="text-xs text-muted-foreground">This Week</p>
                </div>
              </div>
              <div class="flex -space-x-2 overflow-hidden">
                <div v-for="i in 3" :key="i" class="inline-block h-8 w-8 rounded-full ring-2 ring-background bg-muted"></div>
                <div class="inline-block h-8 w-8 rounded-full ring-2 ring-background bg-muted flex items-center justify-center text-xs text-muted-foreground font-medium">+2k</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Brands/Trust Section -->
    <section class="py-10 border-b border-border/40 bg-muted/20">
      <div class="container max-w-7xl">
        <p class="text-center text-sm font-medium text-muted-foreground mb-6">TRUSTED BY MODERN COMPANIES WORLDWIDE</p>
        <div class="flex flex-wrap justify-center gap-8 md:gap-16 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
          <div v-for="i in 5" :key="i" class="h-8 w-24 bg-foreground/20 rounded animate-pulse"></div>
        </div>
      </div>
    </section>

    <!-- Categories Section -->
    <section class="py-20">
      <div class="container max-w-7xl">
        <div class="flex items-end justify-between mb-10">
          <div>
            <h2 class="text-3xl font-bold tracking-tight">Shop by Category</h2>
            <p class="text-muted-foreground mt-2 text-lg">Browse our curated selection</p>
          </div>
          <RouterLink to="/products" class="hidden md:flex text-primary font-medium hover:underline items-center">
            View all categories <ArrowRight class="ml-2 h-4 w-4" />
          </RouterLink>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
          <RouterLink
            v-for="category in productStore.categories"
            :key="category.id"
            :to="`/products?category=${category.id}`"
            class="group relative overflow-hidden rounded-xl aspect-[4/5] bg-muted"
          >
            <div class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors z-10"></div>
            <img 
              :src="`https://source.unsplash.com/random/400x500/?${category.name}`" 
              class="object-cover w-full h-full group-hover:scale-110 transition-transform duration-700"
              alt="Category"
            />
            <div class="absolute bottom-0 left-0 p-4 z-20 text-white w-full">
              <h3 class="font-bold text-xl">{{ category.name }}</h3>
              <p class="text-white/80 text-sm mt-1 transform translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                {{ category.products_count || 0 }} Products
              </p>
            </div>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Featured Products -->
    <section class="py-20 bg-muted/30">
      <div class="container max-w-7xl">
        <div class="flex items-center justify-between mb-10">
          <div>
            <h2 class="text-3xl font-bold tracking-tight">Featured Products</h2>
            <p class="text-muted-foreground mt-2 text-lg">Our most popular picks this week</p>
          </div>
        </div>
        
        <div v-if="productStore.loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="i in 4" :key="i" class="space-y-4">
            <div class="aspect-square bg-muted rounded-2xl animate-pulse"></div>
            <div class="h-4 bg-muted w-2/3 rounded animate-pulse"></div>
            <div class="h-4 bg-muted w-1/3 rounded animate-pulse"></div>
          </div>
        </div>

        <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <RouterLink
            v-for="product in productStore.products.slice(0, 8)"
            :key="product.id"
            :to="`/products/${product.id}`"
            class="group"
          >
            <div class="relative aspect-square overflow-hidden rounded-2xl bg-muted mb-4">
              <img
                v-if="product.image"
                :src="`http://localhost:8000/storage/${product.image}`"
                :alt="product.name"
                class="h-full w-full object-cover object-center group-hover:scale-105 transition-transform duration-500"
              />
              <div v-else class="h-full w-full flex items-center justify-center bg-secondary">
                <span class="text-4xl font-bold text-muted-foreground/20">{{ product.name.charAt(0) }}</span>
              </div>
              
              <!-- Quick Action Overlay -->
              <div class="absolute bottom-4 left-4 right-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                <Button class="w-full bg-white/90 text-black hover:bg-white shadow-lg backdrop-blur-sm">
                  Quick View
                </Button>
              </div>
              
              <Badge v-if="product.category" variant="secondary" class="absolute top-3 left-3 bg-white/90 backdrop-blur-md shadow-sm text-xs font-medium">
                {{ product.category.name }}
              </Badge>
            </div>

            <div class="space-y-1">
              <h3 class="font-medium text-lg leading-tight group-hover:text-primary transition-colors">
                {{ product.name }}
              </h3>
              <p class="text-sm text-muted-foreground line-clamp-1">
                {{ product.description }}
              </p>
              <div class="font-bold text-lg pt-1">
                {{ formatPrice(product.price) }}
              </div>
            </div>
          </RouterLink>
        </div>

        <div class="mt-12 text-center">
          <RouterLink to="/products">
            <Button variant="outline" size="lg" class="px-8 rounded-full">
              View All Products
            </Button>
          </RouterLink>
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-24 bg-card border-t border-border/40">
      <div class="container max-w-4xl text-center space-y-8">
        <h2 class="text-3xl md:text-4xl font-bold tracking-tight">Join the Inner Circle</h2>
        <p class="text-muted-foreground max-w-xl mx-auto text-lg">
          Subscribe to our newsletter to receive exclusive offers, latest news and updates on our new collections.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <input 
            type="email" 
            placeholder="Enter your email address" 
            class="flex-1 h-12 px-4 rounded-full border border-input bg-background focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all"
          />
          <Button size="lg" class="rounded-full px-8">Subscribe</Button>
        </div>
        <p class="text-xs text-muted-foreground">
          By subscribing you agree to our Terms & Conditions and Privacy Policy.
        </p>
      </div>
    </section>
  </div>
</template>
