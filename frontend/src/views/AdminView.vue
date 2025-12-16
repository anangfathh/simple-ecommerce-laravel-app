<script setup lang="ts">
import { RouterView, RouterLink, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import Button from '@/components/ui/Button.vue'
import { 
  LayoutDashboard, 
  Package, 
  Tags, 
  ChevronLeft,
  Menu,
  X,
  ChevronsLeft,
  ChevronsRight
} from 'lucide-vue-next'
import { ref } from 'vue'

const route = useRoute()
const authStore = useAuthStore()
const sidebarOpen = ref(false)
const sidebarCollapsed = ref(false)

const navigation = [
  { name: 'Dashboard', to: '/admin', icon: LayoutDashboard },
  { name: 'Products', to: '/admin/products', icon: Package },
  { name: 'Categories', to: '/admin/categories', icon: Tags },
]

const isActive = (to: string) => {
  if (to === '/admin') {
    return route.path === '/admin'
  }
  return route.path.startsWith(to)
}
</script>

<template>
  <div class="min-h-screen bg-muted/20">
    <!-- Mobile Sidebar Toggle -->
    <div class="lg:hidden fixed top-16 left-4 z-50">
      <Button variant="outline" size="icon" @click="sidebarOpen = !sidebarOpen">
        <Menu v-if="!sidebarOpen" class="h-5 w-5" />
        <X v-else class="h-5 w-5" />
      </Button>
    </div>

    <!-- Sidebar -->
    <aside
      :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'w-64'
      ]"
      class="fixed inset-y-0 left-0 z-40 bg-card border-r border-border transition-all duration-300 pt-16 lg:pt-0"
    >
      <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="p-4 border-b border-border">
          <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
            <RouterLink to="/admin" class="flex items-center gap-2 min-w-0" :class="sidebarCollapsed ? 'justify-center' : ''">
              <div class="h-9 w-9 rounded-lg bg-primary flex items-center justify-center flex-shrink-0">
                <LayoutDashboard class="h-5 w-5 text-primary-foreground" />
              </div>
              <div v-if="!sidebarCollapsed" class="flex flex-col min-w-0">
                <span class="font-bold text-sm truncate">Anangfath Store</span>
                <span class="text-[10px] text-muted-foreground">Admin Panel</span>
              </div>
            </RouterLink>
            <Button 
              v-if="!sidebarCollapsed"
              variant="ghost" 
              size="icon" 
              class="hidden lg:flex h-8 w-8 flex-shrink-0 hover:bg-muted"
              @click="sidebarCollapsed = !sidebarCollapsed"
            >
              <ChevronsLeft class="h-4 w-4" />
            </Button>
          </div>
          <!-- Expand button when collapsed -->
          <Button 
            v-if="sidebarCollapsed"
            variant="ghost" 
            size="icon" 
            class="hidden lg:flex h-8 w-8 mx-auto mt-2 hover:bg-muted"
            @click="sidebarCollapsed = !sidebarCollapsed"
          >
            <ChevronsRight class="h-4 w-4" />
          </Button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1">
          <RouterLink
            v-for="item in navigation"
            :key="item.name"
            :to="item.to"
            :class="[
              'flex items-center rounded-lg transition-colors',
              sidebarCollapsed ? 'justify-center px-3 py-3' : 'space-x-3 px-4 py-3',
              isActive(item.to) 
                ? 'bg-primary text-primary-foreground' 
                : 'text-muted-foreground hover:bg-muted hover:text-foreground'
            ]"
            :title="sidebarCollapsed ? item.name : ''"
            @click="sidebarOpen = false"
          >
            <component :is="item.icon" class="h-5 w-5 flex-shrink-0" />
            <span v-if="!sidebarCollapsed" class="truncate">{{ item.name }}</span>
          </RouterLink>
        </nav>

        <!-- User Info -->
        <div v-if="!sidebarCollapsed" class="p-4 border-t border-border">
          <div class="flex items-center space-x-3 px-4 py-3 rounded-lg bg-muted/50">
            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center flex-shrink-0">
              <span class="font-semibold text-primary">{{ authStore.user?.name.charAt(0) }}</span>
            </div>
            <div class="flex-1 min-w-0">
              <p class="font-medium truncate">{{ authStore.user?.name }}</p>
              <p class="text-xs text-muted-foreground truncate">{{ authStore.user?.email }}</p>
            </div>
          </div>
        </div>
        <div v-else class="p-4 border-t border-border flex justify-center">
          <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
            <span class="font-semibold text-primary">{{ authStore.user?.name.charAt(0) }}</span>
          </div>
        </div>

        <!-- Back to Store -->
        <div class="p-4 border-t border-border">
          <RouterLink to="/" v-if="!sidebarCollapsed">
            <Button variant="outline" class="w-full">
              <ChevronLeft class="h-4 w-4 mr-2" />
              Back to Store
            </Button>
          </RouterLink>
          <RouterLink to="/" v-else>
            <Button variant="outline" size="icon" class="w-full" title="Back to Store">
              <ChevronLeft class="h-4 w-4" />
            </Button>
          </RouterLink>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <main :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'" class="transition-all duration-300">
      <div class="p-6 lg:p-8">
        <RouterView />
      </div>
    </main>

    <!-- Overlay for mobile -->
    <div
      v-if="sidebarOpen"
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
      @click="sidebarOpen = false"
    ></div>
  </div>
</template>
