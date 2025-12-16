<script setup lang="ts">
import { onMounted, ref } from "vue";
import { RouterLink } from "vue-router";
import { useProductStore } from "@/stores/products";
import Button from "@/components/ui/Button.vue";
import Input from "@/components/ui/Input.vue";
import Card from "@/components/ui/Card.vue";
import CardHeader from "@/components/ui/CardHeader.vue";
import CardTitle from "@/components/ui/CardTitle.vue";
import CardContent from "@/components/ui/CardContent.vue";
import Badge from "@/components/ui/Badge.vue";
import { Plus, Search, Edit, Trash2, Package, Loader2, AlertTriangle, X } from "lucide-vue-next";

const productStore = useProductStore();
const searchQuery = ref("");
const deleteModalOpen = ref(false);
const productToDelete = ref<{ id: number; name: string } | null>(null);
const deleting = ref(false);

const formatPrice = (price: number) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
  }).format(price);
};

const handleSearch = () => {
  productStore.fetchProducts({ search: searchQuery.value, per_page: 20 });
};

const openDeleteModal = (id: number, name: string) => {
  productToDelete.value = { id, name };
  deleteModalOpen.value = true;
};

const closeDeleteModal = () => {
  deleteModalOpen.value = false;
  productToDelete.value = null;
};

const confirmDelete = async () => {
  if (!productToDelete.value) return;

  deleting.value = true;
  await productStore.deleteProduct(productToDelete.value.id);
  deleting.value = false;
  closeDeleteModal();
};

onMounted(async () => {
  await productStore.fetchProducts({ per_page: 20 });
  await productStore.fetchCategories();
});
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
      <div>
        <h1 class="text-3xl font-bold">Products</h1>
        <p class="text-muted-foreground mt-1">Manage your product inventory</p>
      </div>
      <RouterLink to="/admin/products/create">
        <Button>
          <Plus class="h-4 w-4 mr-2" />
          Add Product
        </Button>
      </RouterLink>
    </div>

    <!-- Search -->
    <Card>
      <CardContent class="pt-6">
        <div class="flex gap-4">
          <div class="relative flex-1">
            <Search class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground" />
            <Input v-model="searchQuery" placeholder="Search products..." class="pl-10" @keyup.enter="handleSearch" />
          </div>
          <Button @click="handleSearch">Search</Button>
        </div>
      </CardContent>
    </Card>

    <!-- Products Table -->
    <Card>
      <CardHeader>
        <CardTitle>All Products ({{ productStore.pagination.total }})</CardTitle>
      </CardHeader>
      <CardContent>
        <div v-if="productStore.loading" class="flex items-center justify-center py-12">
          <Loader2 class="h-8 w-8 animate-spin text-primary" />
        </div>

        <div v-else-if="productStore.products.length === 0" class="text-center py-12">
          <Package class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
          <h3 class="text-lg font-medium mb-2">No products found</h3>
          <p class="text-muted-foreground mb-4">Get started by adding your first product</p>
          <RouterLink to="/admin/products/create">
            <Button>
              <Plus class="h-4 w-4 mr-2" />
              Add Product
            </Button>
          </RouterLink>
        </div>

        <div v-else class="overflow-x-auto">
          <table class="w-full">
            <thead>
              <tr class="border-b border-border">
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Product</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground hidden md:table-cell">Category</th>
                <th class="text-left py-3 px-4 font-medium text-muted-foreground">Price</th>
                <th class="text-right py-3 px-4 font-medium text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in productStore.products" :key="product.id" class="border-b border-border/50 hover:bg-muted/50 transition-colors">
                <td class="py-4 px-4">
                  <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-lg bg-muted flex items-center justify-center overflow-hidden flex-shrink-0">
                      <img v-if="product.image" :src="`http://localhost:8000/storage/${product.image}`" :alt="product.name" class="w-full h-full object-cover" />
                      <Package v-else class="h-6 w-6 text-muted-foreground" />
                    </div>
                    <div class="min-w-0">
                      <p class="font-medium truncate">{{ product.name }}</p>
                      <p class="text-sm text-muted-foreground truncate max-w-xs">{{ product.description?.substring(0, 50) }}{{ product.description && product.description.length > 50 ? "..." : "" }}</p>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-4 hidden md:table-cell">
                  <Badge variant="secondary">{{ product.category?.name || "Uncategorized" }}</Badge>
                </td>
                <td class="py-4 px-4">
                  <span class="font-medium text-primary">{{ formatPrice(product.price) }}</span>
                </td>
                <td class="py-4 px-4">
                  <div class="flex items-center justify-end space-x-2">
                    <RouterLink :to="`/admin/products/${product.id}/edit`">
                      <Button variant="ghost" size="icon">
                        <Edit class="h-4 w-4" />
                      </Button>
                    </RouterLink>
                    <Button variant="ghost" size="icon" @click="openDeleteModal(product.id, product.name)">
                      <Trash2 class="h-4 w-4" />
                    </Button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </CardContent>
    </Card>

    <!-- Delete Confirmation Modal -->
    <div v-if="deleteModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" @click.self="closeDeleteModal">
      <div class="bg-card border border-border rounded-lg shadow-lg max-w-md w-full animate-in fade-in zoom-in duration-200">
        <div class="p-6 space-y-4">
          <!-- Header -->
          <div class="flex items-start justify-between">
            <div class="flex items-center space-x-3">
              <div class="p-2 rounded-full bg-destructive/10">
                <AlertTriangle class="h-6 w-6 text-destructive" />
              </div>
              <div>
                <h3 class="text-lg font-semibold">Delete Product</h3>
                <p class="text-sm text-muted-foreground">This action cannot be undone</p>
              </div>
            </div>
            <Button variant="ghost" size="icon" @click="closeDeleteModal">
              <X class="h-4 w-4" />
            </Button>
          </div>

          <!-- Content -->
          <div class="space-y-3">
            <p class="text-muted-foreground">Are you sure you want to delete this product?</p>
            <div class="p-3 rounded-lg bg-muted border border-border">
              <p class="font-medium">{{ productToDelete?.name }}</p>
              <p class="text-xs text-muted-foreground mt-1">Product ID: #{{ productToDelete?.id }}</p>
            </div>
            <p class="text-sm text-destructive">⚠️ This will permanently delete the product and all associated data.</p>
          </div>

          <!-- Actions -->
          <div class="flex gap-3 pt-2">
            <Button variant="outline" class="flex-1" @click="closeDeleteModal" :disabled="deleting"> Cancel </Button>
            <Button variant="destructive" class="flex-1" @click="confirmDelete" :disabled="deleting">
              <Loader2 v-if="deleting" class="h-4 w-4 mr-2 animate-spin" />
              Delete Product
            </Button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
