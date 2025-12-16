<script setup lang="ts">
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
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
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const errors = ref<Record<string, string>>({})

const handleSubmit = async () => {
  errors.value = {}
  
  // Validation
  if (!name.value) {
    errors.value.name = 'Name is required'
  }
  if (!email.value) {
    errors.value.email = 'Email is required'
  }
  if (!password.value) {
    errors.value.password = 'Password is required'
  } else if (password.value.length < 8) {
    errors.value.password = 'Password must be at least 8 characters'
  }
  if (password.value !== passwordConfirmation.value) {
    errors.value.passwordConfirmation = 'Passwords do not match'
  }
  
  if (Object.keys(errors.value).length > 0) return
  
  const success = await authStore.register(
    name.value,
    email.value,
    password.value,
    passwordConfirmation.value
  )
  
  if (success) {
    router.push('/')
  } else {
    errors.value.general = authStore.error || 'Registration failed'
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
          <CardTitle class="text-2xl">Create an account</CardTitle>
          <CardDescription>Enter your details to get started</CardDescription>
        </CardHeader>
        <CardContent>
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- General Error -->
            <div v-if="errors.general" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
              {{ errors.general }}
            </div>

            <!-- Name -->
            <div class="space-y-2">
              <Label for="name">Full Name</Label>
              <Input
                id="name"
                v-model="name"
                placeholder="John Doe"
                :class="errors.name ? 'border-destructive' : ''"
              />
              <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
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

            <!-- Confirm Password -->
            <div class="space-y-2">
              <Label for="passwordConfirmation">Confirm Password</Label>
              <Input
                id="passwordConfirmation"
                v-model="passwordConfirmation"
                type="password"
                placeholder="••••••••"
                :class="errors.passwordConfirmation ? 'border-destructive' : ''"
              />
              <p v-if="errors.passwordConfirmation" class="text-sm text-destructive">{{ errors.passwordConfirmation }}</p>
            </div>

            <!-- Submit -->
            <Button type="submit" class="w-full" :disabled="authStore.loading">
              <Loader2 v-if="authStore.loading" class="h-4 w-4 mr-2 animate-spin" />
              {{ authStore.loading ? 'Creating account...' : 'Create Account' }}
            </Button>
          </form>
        </CardContent>
        <CardFooter class="flex-col space-y-4">
          <p class="text-sm text-muted-foreground text-center">
            Already have an account?
            <RouterLink to="/login" class="text-primary hover:underline font-medium">
              Sign in
            </RouterLink>
          </p>
        </CardFooter>
      </Card>
    </div>
  </div>
</template>
