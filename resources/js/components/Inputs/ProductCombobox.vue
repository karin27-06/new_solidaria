<template>
  <!-- Loading state -->
  <div v-if="isLoading" class="flex items-center space-x-2 py-2">
    <svg class="h-4 w-4 animate-spin text-blue-600" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z" />
    </svg>
    <span class="text-sm text-gray-500">Loading products...</span>
  </div>

  <!-- Error message -->
  <div v-else-if="error" class="py-2 text-sm text-red-600">Error loading products. Please try again.</div>

  <!-- Combobox -->
  <div v-else class="relative w-full">
    <!-- Input and icons -->
    <div class="relative">
      <input
        v-model="searchText"
        type="text"
        placeholder="Search products..."
        class="w-full rounded-md border border-gray-300 bg-white pl-10 pr-10 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        @input="handleSearchInput"
        @focus="isOpen = true"
        @keydown.enter="selectFirstProduct"
        @keydown.arrow-down.prevent="moveHighlight(1)"
        @keydown.arrow-up.prevent="moveHighlight(-1)"
        @keydown.escape="closeDropdown"
      />
      <!-- Search icon -->
      <svg
        class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
      <!-- Loading spinner during search -->
      <svg
        v-if="isSearching"
        class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-blue-600"
        viewBox="0 0 24 24"
      >
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z" />
      </svg>
      <!-- Clear selection button -->
      <button
        v-if="!isSearching && selectedProduct"
        class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
        @click="clearSelection"
        title="Clear selection"
      >
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <!-- Dropdown -->
    <div
      v-if="isOpen"
      class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg"
    >
      <div
        v-if="filteredProducts.length"
        v-for="(product, index) in filteredProducts"
        :key="product.id"
        class="flex cursor-pointer items-center px-4 py-2 text-sm text-gray-900"
        :class="{
          'bg-blue-50': index === highlightedIndex,
          'bg-blue-100': selectedProduct?.id === product.id,
        }"
        @click="selectProduct(product)"
        @mousemove="highlightedIndex = index"
      >
        {{ product.name }}
        <svg
          v-if="selectedProduct?.id === product.id"
          class="ml-auto h-4 w-4 text-blue-600"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </div>
      <div
        v-else-if="searchText || isSearching"
        class="px-4 py-2 text-sm text-gray-500"
      >
        {{ isSearching ? 'Searching...' : 'No products found.' }}
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, defineExpose } from 'vue';
import debounce from 'debounce';
import { ProductServices } from '@/services/productService';
import { ProductResource } from '@/pages/panel/product/interface/Product';

const emit = defineEmits<{
  (e: 'select', product: ProductResource): void;
}>();

// State
const products = ref<ProductResource[]>([]);
const searchText = ref<string>('');
const error = ref<boolean>(false);
const isLoading = ref<boolean>(true);
const isSearching = ref<boolean>(false);
const selectedProduct = ref<ProductResource | null>(null);
const initialLoadDone = ref<boolean>(false);
const isOpen = ref<boolean>(false);
const highlightedIndex = ref<number>(-1);

const filteredProducts = computed(() => {
  if (!searchText.value) return products.value;

  return products.value.filter((product) =>
    product.name.toLowerCase().includes(searchText.value.toLowerCase())
  );
});

const initialLoadProducts = async () => {
  if (initialLoadDone.value) return;

  try {
    isLoading.value = true;
    const response: ProductResource[] = await ProductServices.getProducts('');
    products.value = response || [];
    error.value = false;
    initialLoadDone.value = true;
  } catch (e) {
    console.error('Error loading products:', e);
    error.value = true;
  } finally {
    isLoading.value = false;
  }
};

const searchProducts = async (query: string) => {
  if (!initialLoadDone.value) return;

  try {
    isSearching.value = true;
    const response: ProductResource[] = await ProductServices.getProducts(query);
    products.value = response || [];
    error.value = false;
  } catch (e) {
    console.error('Error searching products:', e);
    error.value = true;
  } finally {
    isSearching.value = false;
  }
};

const handleSearchInput = () => {
  isOpen.value = true;
  highlightedIndex.value = -1;
  if (initialLoadDone.value) {
    debouncedSearch(searchText.value);
  }
};

const debouncedSearch = debounce((value: string) => {
  if (value.length >= 3 || value === '') {
    searchProducts(value);
  }
}, 400);

const selectProduct = (product: ProductResource) => {
  selectedProduct.value = product;
  searchText.value = product.name;
  isOpen.value = false;
  highlightedIndex.value = -1;
  emit('select', product);
};

const selectFirstProduct = () => {
  if (filteredProducts.value.length) {
    selectProduct(filteredProducts.value[0]);
  }
};

const moveHighlight = (direction: number) => {
  const maxIndex = filteredProducts.value.length - 1;
  let newIndex = highlightedIndex.value + direction;

  if (newIndex < -1) newIndex = maxIndex;
  if (newIndex > maxIndex) newIndex = -1;

  highlightedIndex.value = newIndex;
  isOpen.value = true;
};

const closeDropdown = () => {
  isOpen.value = false;
  highlightedIndex.value = -1;
  if (selectedProduct.value) {
    searchText.value = selectedProduct.value.name;
  } else {
    searchText.value = '';
  }
};

const clearSelection = () => {
  selectedProduct.value = null;
  searchText.value = '';
  isOpen.value = false;
  highlightedIndex.value = -1;
  emit('select', null as any); // Emit null to clear selection
};

// Expose reset method to clear selection
const reset = () => {
  clearSelection();
};

defineExpose({ reset });

onMounted(() => {
  initialLoadProducts();
});
</script>

<style scoped>
/* Smooth transitions for dropdown */
.transition-all {
  transition: all 0.2s ease-in-out;
}

/* Ensure dropdown scrolls nicely */
.max-h-60 {
  max-height: 15rem;
}
</style>