<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardFooter from '@/components/ui/CardFooter.vue'
import { ShoppingCart, Loader2 } from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  errors.value = {}
  
  // Basic validation
  if (!email.value) {
    errors.value.email = 'Email is required'
  }
  if (!password.value) {
    errors.value.password = 'Password is required'
  }
  
  if (Object.keys(errors.value).length > 0) return
  
  const success = await authStore.login(email.value, password.value)
  
  if (success) {
    const redirect = route.query.redirect as string || '/'
    router.push(redirect)
  } else {
    errors.value.general = authStore.error || 'Login failed'
  }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-violet-600/10 via-purple-600/5 to-indigo-600/10 py-12 px-4">
    <div class="w-full max-w-md">
      <!-- Logo -->
      <div class="text-center mb-8">
        <RouterLink to="/" class="inline-flex items-center space-x-2">
          <ShoppingCart class="h-10 w-10 text-primary" />
          <span class="font-bold text-2xl gradient-text">ShopVue</span>
        </RouterLink>
      </div>

      <Card class="animate-slide-in-from-bottom">
        <CardHeader class="text-center">
          <CardTitle class="text-2xl">Welcome back</CardTitle>
          <CardDescription>Enter your credentials to access your account</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- General Error -->
            <div v-if="errors.general" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
              {{ errors.general }}
            </div>

            <!-- Email -->
            <div class="space-y-2">
              <Label for="email">Email</Label>
              <Input
                id="email"
                v-model="email"
                type="email"
                placeholder="john@example.com"
                :class="errors.email ? 'border-destructive' : ''"
              />
              <p v-if="errors.email" class="text-sm text-destructive">{{ errors.email }}</p>
            </div>

            <!-- Password -->
            <div class="space-y-2">
              <Label for="password">Password</Label>
              <Input
                id="password"
                v-model="password"
                type="password"
                placeholder="••••••••"
                :class="errors.password ? 'border-destructive' : ''"
              />
              <p v-if="errors.password" class="text-sm text-destructive">{{ errors.password }}</p>
            </div>

            <!-- Submit -->
            <Button type="submit" class="w-full" :disabled="authStore.loading">
              <Loader2 v-if="authStore.loading" class="h-4 w-4 mr-2 animate-spin" />
              {{ authStore.loading ? 'Signing in...' : 'Sign In' }}
            </Button>
          </form>

          <!-- Demo Credentials -->
          <div class="mt-6 p-4 bg-muted/50 rounded-lg">
            <p class="text-sm font-medium mb-2">Demo Credentials:</p>
            <div class="text-xs text-muted-foreground space-y-1">
              <p><strong>Admin:</strong> admin@example.com / password</p>
              <p><strong>User:</strong> user@example.com / password</p>
            </div>
          </div>
        </CardContent>
        <CardFooter class="flex-col space-y-4">
          <p class="text-sm text-muted-foreground text-center">
            Don't have an account?
            <RouterLink to="/register" class="text-primary hover:underline font-medium">
              Sign up
            </RouterLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
