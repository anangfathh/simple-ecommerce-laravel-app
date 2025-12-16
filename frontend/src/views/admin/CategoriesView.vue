<script setup lang="ts">
import { onMounted, ref } from 'vue'
import { useProductStore } from '@/stores/products'
import Button from '@/components/ui/Button.vue'
import Input from '@/components/ui/Input.vue'
import Label from '@/components/ui/Label.vue'
import Textarea from '@/components/ui/Textarea.vue'
import Card from '@/components/ui/Card.vue'
import CardHeader from '@/components/ui/CardHeader.vue'
import CardTitle from '@/components/ui/CardTitle.vue'
import CardContent from '@/components/ui/CardContent.vue'
import Badge from '@/components/ui/Badge.vue'
import { Plus, Trash2, Tags, Loader2, X } from 'lucide-vue-next'

const productStore = useProductStore()

const showForm = ref(false)
const form = ref({
  name: '',
  description: '',
})
const loading = ref(false)
const deleteConfirm = ref<number | null>(null)
const deleting = ref(false)
const errors = ref<Record<string, string>>({})

const resetForm = () => {
  form.value = { name: '', description: '' }
  errors.value = {}
  showForm.value = false
}

const handleSubmit = async () => {
  errors.value = {}
  
  if (!form.value.name.trim()) {
    errors.value.name = 'Name is required'
    return
  }
  
  loading.value = true
  
  try {
    await productStore.createCategory({
      name: form.value.name,
      description: form.value.description,
    })
    resetForm()
  } catch (error: any) {
    errors.value.general = error.response?.data?.message || 'Failed to create category'
  } finally {
    loading.value = false
  }
}

const handleDelete = async (id: number) => {
  if (deleteConfirm.value !== id) {
    deleteConfirm.value = id
    return
  }
  
  deleting.value = true
  await productStore.deleteCategory(id)
  deleteConfirm.value = null
  deleting.value = false
}

onMounted(async () => {
  await productStore.fetchCategories()
})
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Categories</h1>
        <p class="text-muted-foreground mt-1">Manage product categories</p>
      </div>
      <Button @click="showForm = !showForm">
        <Plus v-if="!showForm" class="h-4 w-4 mr-2" />
        <X v-else class="h-4 w-4 mr-2" />
        {{ showForm ? 'Cancel' : 'Add Category' }}
      </Button>
    </div>

    <!-- Add Category Form -->
    <Card v-if="showForm" class="animate-slide-in-from-top">
      <CardHeader>
        <CardTitle>Create New Category</CardTitle>
      </CardHeader>
      <form @submit.prevent="handleSubmit">
        <CardContent class="space-y-4">
          <div v-if="errors.general" class="p-3 text-sm text-destructive bg-destructive/10 rounded-md">
            {{ errors.general }}
          </div>

          <div class="space-y-2">
            <Label for="name">Category Name *</Label>
            <Input
              id="name"
              v-model="form.name"
              placeholder="Enter category name"
              :class="errors.name ? 'border-destructive' : ''"
            />
            <p v-if="errors.name" class="text-sm text-destructive">{{ errors.name }}</p>
          </div>

          <div class="space-y-2">
            <Label for="description">Description</Label>
            <Textarea
              id="description"
              v-model="form.description"
              placeholder="Enter category description"
              :rows="3"
            />
          </div>

          <div class="flex justify-end space-x-2">
            <Button type="button" variant="outline" @click="resetForm">
              Cancel
            </Button>
            <Button type="submit" :disabled="loading">
              <Loader2 v-if="loading" class="h-4 w-4 mr-2 animate-spin" />
              {{ loading ? 'Creating...' : 'Create Category' }}
            </Button>
          </div>
        </CardContent>
      </form>
    </Card>

    <!-- Categories List -->
    <Card>
      <CardHeader>
        <CardTitle>All Categories ({{ productStore.categories.length }})</CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="productStore.categories.length === 0" class="text-center py-12">
          <Tags class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
          <h3 class="text-lg font-medium mb-2">No categories found</h3>
          <p class="text-muted-foreground mb-4">Get started by creating your first category</p>
          <Button @click="showForm = true">
            <Plus class="h-4 w-4 mr-2" />
            Add Category
          </Button>
        </div>

        <div v-else class="space-y-3">
          <div
            v-for="category in productStore.categories"
            :key="category.id"
            class="flex items-center justify-between p-4 rounded-lg border border-border hover:bg-muted/50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                <Tags class="h-6 w-6 text-primary" />
              </div>
              <div>
                <h3 class="font-medium">{{ category.name }}</h3>
                <p class="text-sm text-muted-foreground">
                  {{ category.description || 'No description' }}
                </p>
              </div>
            </div>
            <div class="flex items-center space-x-4">
              <Badge variant="secondary">
                {{ category.products_count || 0 }} products
              </Badge>
              <Button
                :variant="deleteConfirm === category.id ? 'destructive' : 'ghost'"
                size="icon"
                :disabled="deleting"
                @click="handleDelete(category.id)"
              >
                <Loader2 v-if="deleting && deleteConfirm === category.id" class="h-4 w-4 animate-spin" />
                <Trash2 v-else class="h-4 w-4" />
              </Button>
            </div>
          </div>
        </div>

        <p v-if="deleteConfirm" class="mt-4 text-sm text-muted-foreground text-center">
          Click the delete button again to confirm. Warning: This will delete all products in this category.
        </p>
      </CardContent>
    </Card>
  </div>
</template>
