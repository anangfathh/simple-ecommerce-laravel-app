<script setup lang="ts">
import { onMounted, ref, computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProductStore } from '@/stores/products'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Textarea from '@/components/ui/Textarea.vue'
import Select from '@/components/ui/Select.vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardDescription from '@/components/ui/CardDescription.vue'
import CardContent from '@/components/ui/CardContent.vue'
import CardFooter from '@/components/ui/CardFooter.vue'
import { ChevronLeft, Loader2, Upload, X } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const productStore = useProductStore()

const isEditing = computed(() => !!route.params.id)
const productId = computed(() => Number(route.params.id))

const form = ref({
  name: '',
  description: '',
  price: 0,
  category_id: '',
})

const imageFile = ref<File | null>(null)
const imagePreview = ref<string | null>(null)
const loading = ref(false)
const errors = ref<Record<string, string>>({})

const categoryOptions = computed(() => [
  { value: '', label: 'Select a category' },
  ...productStore.categories.map(c => ({
    value: c.id,
    label: c.name,
  })),
])

const handleImageChange = (event: Event) => {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]
  
  if (file) {
    imageFile.value = file
    imagePreview.value = URL.createObjectURL(file)
  }
}

const removeImage = () => {
  imageFile.value = null
  imagePreview.value = null
}

const validate = () => {
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'Name is required'
  }
  if (!form.value.price || form.value.price <= 0) {
    errors.value.price = 'Price must be greater than 0'
  }
  if (!form.value.category_id) {
    errors.value.category_id = 'Category is required'
  }
  
  return Object.keys(errors.value).length === 0
}

const handleSubmit = async () => {
  if (!validate()) return
  
  loading.value = true
  
  try {
    const formData = new FormData()
    formData.append('name', form.value.name)
    formData.append('description', form.value.description)
    formData.append('price', form.value.price.toString())
    formData.append('category_id', form.value.category_id.toString())
    
    if (imageFile.value) {
      formData.append('image', imageFile.value)
    }
    
    if (isEditing.value) {
      // For PUT request with FormData, we need to use _method
      formData.append('_method', 'PUT')
      await productStore.updateProduct(productId.value, formData)
    } else {
      await productStore.createProduct(formData)
    }
    
    router.push('/admin/products')
  } catch (error: any) {
    if (error.response?.data?.errors) {
      errors.value = error.response.data.errors
    } else {
      errors.value.general = error.response?.data?.message || 'Failed to save product'
    }
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await productStore.fetchCategories()
  
  if (isEditing.value) {
    const product = await productStore.fetchProduct(productId.value)
    if (product) {
      form.value = {
        name: product.name,
        description: product.description || '',
        price: product.price,
        category_id: product.category_id.toString(),
      }
      if (product.image) {
        imagePreview.value = `http://localhost:8000/storage/${product.image}`
      }
    }
  }
})
</script>

<template>
  <div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Link -->
    <Button variant="ghost" @click="router.push('/admin/products')">
      <ChevronLeft class="h-4 w-4 mr-1" />
      Back to Products
    </Button>

    <Card>
      <CardHeader>
        <CardTitle>{{ isEditing ? 'Edit Product' : 'Create New Product' }}</CardTitle>
        <CardDescription>
          {{ isEditing ? 'Update the product details below' : 'Fill in the details to add a new product' }}
        </CardDescription>
      </CardHeader>
      <form @submit.prevent="handleSubmit">
        <CardContent class="space-y-6">
          <!-- General Error -->
          <div v-if="errors.general" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
            {{ errors.general }}
          </div>

          <!-- Name -->
          <div class="space-y-2">
            <Label for="name">Product Name *</Label>
            <Input
              id="name"
              v-model="form.name"
              placeholder="Enter product name"
              :class="errors.name ? 'border-destructive' : ''"
            />
            <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
          </div>

          <!-- Description -->
          <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
              id="description"
              v-model="form.description"
              placeholder="Enter product description"
              :rows="4"
            />
          </div>

          <!-- Price & Category -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-2">
              <Label for="price">Price ($) *</Label>
              <Input
                id="price"
                v-model.number="form.price"
                type="number"
                step="0.01"
                min="0"
                placeholder="0.00"
                :class="errors.price ? 'border-destructive' : ''"
              />
              <p v-if="errors.price" class="text-sm text-destructive">{{ errors.price }}</p>
            </div>

            <div class="space-y-2">
              <Label for="category">Category *</Label>
              <Select
                id="category"
                v-model="form.category_id"
                :options="categoryOptions"
                placeholder="Select a category"
                :class="errors.category_id ? 'border-destructive' : ''"
              />
              <p v-if="errors.category_id" class="text-sm text-destructive">{{ errors.category_id }}</p>
            </div>
          </div>

          <!-- Image -->
          <div class="space-y-2">
            <Label>Product Image</Label>
            <div class="border-2 border-dashed border-border rounded-lg p-6 text-center hover:border-primary/50 transition-colors">
              <div v-if="imagePreview" class="relative inline-block">
                <img
                  :src="imagePreview"
                  alt="Preview"
                  class="max-h-48 rounded-lg mx-auto"
                />
                <Button
                  type="button"
                  variant="destructive"
                  size="icon"
                  class="absolute -top-2 -right-2"
                  @click="removeImage"
                >
                  <X class="h-4 w-4" />
                </Button>
              </div>
              <div v-else>
                <Upload class="h-8 w-8 mx-auto text-muted-foreground mb-2" />
                <p class="text-sm text-muted-foreground mb-2">
                  Click to upload or drag and drop
                </p>
                <p class="text-xs text-muted-foreground">
                  PNG, JPG, GIF up to 2MB
                </p>
              </div>
              <input
                type="file"
                accept="image/*"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                :class="{ 'hidden': imagePreview }"
                @change="handleImageChange"
              />
            </div>
          </div>
        </CardContent>
        <CardFooter class="flex justify-end space-x-4">
          <Button type="button" variant="outline" @click="router.push('/admin/products')">
            Cancel
          </Button>
          <Button type="submit" :disabled="loading">
            <Loader2 v-if="loading" class="h-4 w-4 mr-2 animate-spin" />
            {{ loading ? 'Saving...' : (isEditing ? 'Update Product' : 'Create Product') }}
          </Button>
        </CardFooter>
      </form>
    </Card>
  </div>
</template>
