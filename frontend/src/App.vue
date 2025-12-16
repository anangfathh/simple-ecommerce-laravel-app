<script setup lang="ts">
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Button from '@/components/ui/Button.vue'
import { ShoppingBag, LogOut, Menu, X, Shield, Search } from 'lucide-vue-next'
import { ref, computed } from 'vue'

const authStore = useAuthStore()
const route = useRoute()
const mobileMenuOpen = ref(false)

const navigation = computed(() => [
  { name: 'Home', to: '/' },
  { name: 'Shop', to: '/products' },
  { name: 'New Arrivals', to: '/products?sort=latest' },
  { name: 'Collections', to: '/categories' },
])

const handleLogout = async () => {
  await authStore.logout()
  mobileMenuOpen.value = false
}
</script>

<template>
  <div class="min-h-screen bg-background font-sans text-foreground flex flex-col">
    <!-- Header -->
    <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-white/80 backdrop-blur-xl supports-[backdrop-filter]:bg-white/80 dark:bg-black/80">
      <div class="container flex h-20 max-w-7xl items-center justify-between px-4">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2 group">
          <div class="bg-primary text-primary-foreground p-2 rounded-xl group-hover:scale-110 transition-transform">
            <ShoppingBag class="h-5 w-5" />
          </div>
          <span class="font-heading font-bold text-2xl tracking-tight text-primary">
            LuxeStore
          </span>
        </RouterLink>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center gap-8">
          <RouterLink
            v-for="item in navigation"
            :key="item.name"
            :to="item.to"
            class="text-sm font-medium transition-colors hover:text-primary relative group py-2"
            :class="route.path === item.to ? 'text-primary' : 'text-muted-foreground'"
          >
            {{ item.name }}
            <span 
              class="absolute inset-x-0 -bottom-px h-px bg-primary scale-x-0 group-hover:scale-x-100 transition-transform"
              :class="route.path === item.to ? 'scale-x-100' : ''"
            ></span>
          </RouterLink>
        </nav>

        <!-- Auth & Actions -->
        <div class="flex items-center gap-2">
          <Button variant="ghost" size="icon" class="hidden sm:flex text-muted-foreground hover:text-primary">
            <Search class="h-5 w-5" />
          </Button>

          <template v-if="authStore.isAuthenticated">
            <RouterLink v-if="authStore.isAdmin" to="/admin">
              <Button variant="ghost" size="sm" class="hidden sm:flex gap-2">
                <Shield class="h-4 w-4" />
                <span>Dashboard</span>
              </Button>
            </RouterLink>
            
            <div class="hidden sm:flex items-center gap-2 pl-2 border-l border-border/40 ml-2">
              <div class="text-right hidden lg:block">
                <p class="text-xs text-muted-foreground font-medium">Hello,</p>
                <p class="text-sm font-semibold leading-none">{{ authStore.user?.name.split(' ')[0] }}</p>
              </div>
              <Button variant="ghost" size="icon" @click="handleLogout" class="text-destructive hover:bg-destructive/10">
                <LogOut class="h-5 w-5" />
              </Button>
            </div>
          </template>
          
          <template v-else>
            <div class="flex items-center gap-2">
              <RouterLink to="/login">
                <Button variant="ghost" class="font-medium">Sign In</Button>
              </RouterLink>
              <RouterLink to="/register" class="hidden sm:block">
                <Button class="rounded-full px-6">Get Started</Button>
              </RouterLink>
            </div>
          </template>

          <!-- Mobile Menu Button -->
          <Button variant="ghost" size="icon" class="md:hidden ml-1" @click="mobileMenuOpen = !mobileMenuOpen">
            <Menu v-if="!mobileMenuOpen" class="h-6 w-6" />
            <X v-else class="h-6 w-6" />
          </Button>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div v-if="mobileMenuOpen" class="md:hidden border-t border-border/40 bg-background absolute w-full left-0 animate-in slide-in-from-top-4 fade-in duration-200 shadow-xl">
        <div class="container py-6 px-4 space-y-4">
          <nav class="space-y-2">
            <RouterLink
              v-for="item in navigation"
              :key="item.name"
              :to="item.to"
              class="block p-3 text-base font-medium transition-colors hover:bg-muted rounded-lg"
              :class="route.path === item.to ? 'text-primary bg-muted/50' : 'text-muted-foreground'"
              @click="mobileMenuOpen = false"
            >
              {{ item.name }}
            </RouterLink>
          </nav>
          
          <div v-if="authStore.isAuthenticated" class="pt-4 border-t border-border/40 space-y-3">
             <div class="flex items-center gap-3 p-3 bg-muted/50 rounded-lg">
                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                  {{ authStore.user?.name.charAt(0) }}
                </div>
                <div>
                  <p class="font-medium">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-muted-foreground">{{ authStore.user?.email }}</p>
                </div>
             </div>
             <Button variant="destructive" class="w-full justify-start" @click="handleLogout">
               <LogOut class="mr-2 h-4 w-4" /> Sign Out
             </Button>
          </div>
          <div v-else class="pt-4 border-t border-border/40 grid grid-cols-2 gap-3">
            <RouterLink to="/login" class="w-full">
              <Button variant="outline" class="w-full justify-center">Sign In</Button>
            </RouterLink>
            <RouterLink to="/register" class="w-full">
              <Button class="w-full justify-center">Sign Up</Button>
            </RouterLink>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1">
      <RouterView />
    </main>

    <!-- Footer -->
    <footer class="bg-primary text-primary-foreground border-t border-white/10">
      <div class="container max-w-7xl px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 lg:gap-8">
          <div class="col-span-1 md:col-span-2 space-y-6">
            <RouterLink to="/" class="flex items-center gap-2">
              <div class="bg-white text-primary p-2 rounded-xl">
                <ShoppingBag class="h-5 w-5" />
              </div>
              <span class="font-heading font-bold text-2xl tracking-tight">LuxeStore</span>
            </RouterLink>
            <p class="text-primary-foreground/70 max-w-sm leading-relaxed">
              Experience the future of online shopping. Hand-picked collections, 
              premium quality, and a shopping experience like no other.
            </p>
            <div class="flex gap-4">
              <!-- Social Placeholders -->
              <a href="#" class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white hover:text-primary transition-all">
                <span class="sr-only">Twitter</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" /></svg>
              </a>
              <a href="#" class="h-10 w-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white hover:text-primary transition-all">
                <span class="sr-only">Instagram</span>
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.067-.06-1.407-.06-4.123v-.08c0-2.643.012-2.987.06-4.043.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772 4.902 4.902 0 011.772-1.153c.636-.247 1.363-.416 2.427-.465 1.067-.047 1.407-.06 4.123-.06h.08zm-1.634 7.676a.75.75 0 10-1.06-1.06 7.5 7.5 0 01-10.6 0 .75.75 0 00-1.06 1.06 9 9 0 0012.72 0z" clip-rule="evenodd" /><path fill-rule="evenodd" d="M12 7a5 5 0 100 10 5 5 0 000-10zm0 1.5a3.5 3.5 0 110 7 3.5 3.5 0 010-7z" clip-rule="evenodd" /></svg>
              </a>
            </div>
          </div>
          <div>
            <h4 class="font-heading font-semibold text-lg mb-6">Explore</h4>
            <ul class="space-y-4 text-sm text-primary-foreground/70">
              <li><RouterLink to="/products" class="hover:text-white transition-colors">New Arrivals</RouterLink></li>
              <li><RouterLink to="/categories" class="hover:text-white transition-colors">Collections</RouterLink></li>
              <li><RouterLink to="/products" class="hover:text-white transition-colors">Accessories</RouterLink></li>
              <li><RouterLink to="/products" class="hover:text-white transition-colors">Sale Items</RouterLink></li>
            </ul>
          </div>
          <div>
            <h4 class="font-heading font-semibold text-lg mb-6">Company</h4>
            <ul class="space-y-4 text-sm text-primary-foreground/70">
              <li><a href="#" class="hover:text-white transition-colors">About Us</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Careers</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Sustainability</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Terms of Service</a></li>
            </ul>
          </div>
        </div>
        <div class="mt-16 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-primary-foreground/50">
          <p>&copy; {{ new Date().getFullYear() }} LuxeStore Inc. All rights reserved.</p>
          <div class="flex gap-6">
            <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
            <a href="#" class="hover:text-white transition-colors">Cookie Policy</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>
