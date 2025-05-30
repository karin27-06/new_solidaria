<template>
  <!-- Error message -->
  <div v-if="errorType" class="py-2 text-sm text-red-600">{{ errorMessage }}</div>

  <!-- Combobox -->
  <div class="relative w-full">
    <!-- Input and icons -->
    <div class="relative">
      <input
        ref="inputRef"
        :value="modelValue"
        type="text"
        placeholder="Type to search products... (min 3 characters)"
        class="w-full rounded-md border border-gray-300 bg-white pl-10 pr-10 py-2 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
        :name="name"
        :id="id"
        :aria-describedby="ariaDescribedby"
        :aria-invalid="ariaInvalid"
        @input="handleInput"
        @blur="$emit('blur', $event)"
        @change="$emit('change', $event)"
        @focus="handleFocus"
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
      v-if="isOpen && shouldShowDropdown"
      class="absolute z-10 mt-1 max-h-60 w-full overflow-y-auto rounded-md border border-gray-200 bg-white shadow-lg"
    >
      <!-- Results -->
      <div
        v-if="validProducts.length && !isSearching"
        v-for="(product, index) in validProducts"
        :key="product.id"
        class="flex cursor-pointer items-center px-4 py-2 text-sm text-gray-900 hover:bg-gray-50"
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

      <!-- Loading state -->
      <div v-if="isSearching" class="px-4 py-3 text-sm text-gray-500 text-center">
        <div class="flex items-center justify-center space-x-2">
          <svg class="h-4 w-4 animate-spin text-blue-600" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8h8a8 8 0 01-16 0z" />
          </svg>
          <span>Searching...</span>
        </div>
      </div>

      <!-- No results -->
      <div
        v-else-if="!validProducts.length && hasSearched && !isSearching"
        class="px-4 py-3 text-sm text-gray-500 text-center"
      >
        No products found for "{{ modelValue }}"
      </div>

      <!-- Instructions -->
      <div
        v-else-if="!hasSearched && modelValue.length < 3"
        class="px-4 py-3 text-sm text-gray-500 text-center"
      >
        Type at least 3 characters to search
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, defineExpose, onMounted, onUnmounted } from 'vue';
import debounce from 'debounce';
import { ProductServices } from '@/services/productService';

// Updated ProductResource interface to match API response
interface ProductResource {
  id: number;
  name: string;
  laboratory?: string;
  fraction?: number;
  state_fraction?: boolean;
  state_tax?: boolean;
  type?: string; // Added to match potential API field
}

const props = defineProps<{
  modelValue: string;
  name?: string;
  id?: string;
  initialId?: number | null;
  ariaDescribedby?: string;
  ariaInvalid?: boolean | string;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: string): void;
  (e: 'select', product: ProductResource | null): void;
  (e: 'blur', event: FocusEvent): void;
  (e: 'input', event: Event): void;
  (e: 'change', event: Event): void;
}>();

const inputRef = ref<HTMLInputElement | null>(null);
const products = ref<ProductResource[]>([]);
const errorType = ref<boolean>(false);
const errorMessage = ref<string>('Error loading products. Please try again.');
const isSearching = ref<boolean>(false);
const selectedProduct = ref<ProductResource | null>(null);
const isOpen = ref<boolean>(false);
const highlightedIndex = ref<number>(-1);
const hasSearched = ref<boolean>(false);

const shouldShowDropdown = computed(() => {
  return props.modelValue.length >= 3 || hasSearched.value || isSearching.value;
});

// Filter valid products to prevent rendering invalid items
const validProducts = computed(() => {
  return products.value.filter((product): product is ProductResource => 
    product !== null && 
    typeof product === 'object' && 
    'id' in product && 
    'name' in product && 
    typeof product.id === 'number' && 
    typeof product.name === 'string'
  );
});

// Helper function to create a proper input event for vee-validate
const createInputEvent = (value: string): Event => {
  if (inputRef.value) {
    inputRef.value.value = value;
    const event = new Event('input', { bubbles: true });
    Object.defineProperty(event, 'target', {
      value: inputRef.value,
      writable: false
    });
    return event;
  }
  return new Event('input');
};

const searchProducts = async (query: string) => {
  if (query.length < 3) {
    products.value = [];
    hasSearched.value = false;
    return;
  }

  try {
    isSearching.value = true;
    errorType.value = false;
    const response = await ProductServices.getProducts(query);
   // console.log('API Response:', response); // Debug API response
    products.value = Array.isArray(response.products) ? response.products : [];
    hasSearched.value = true;
    if (!products.value.length) {
      errorType.value = true;
      errorMessage.value = 'No products found for the query.';
    }
  } catch (e) {
    console.error('Error searching products:', e);
    errorType.value = true;
    errorMessage.value = 'Failed to load products. Please try again.';
    products.value = [];
  } finally {
    isSearching.value = false;
  }
};

const debouncedSearch = debounce((query: string) => {
  searchProducts(query);
}, 500);

const handleInput = (event: Event) => {
  const value = (event.target as HTMLInputElement).value;
  emit('update:modelValue', value);
  emit('input', event);
  highlightedIndex.value = -1;

  if (value.length < 3) {
    products.value = [];
    hasSearched.value = false;
    isOpen.value = false;
    return;
  }

  isOpen.value = true;
  debouncedSearch(value);
};

const handleFocus = () => {
  if (props.modelValue.length >= 3) {
    isOpen.value = true;
  }
};

const selectProduct = (product: ProductResource | null) => {
  if (!product || !product.name || typeof product.id !== 'number') {
    console.warn('Invalid product selected:', product);
    return;
  }

  selectedProduct.value = product;
  emit('update:modelValue', product.name);
  emit('select', product);
  
  const inputEvent = createInputEvent(product.name);
  emit('change', inputEvent);
  
  isOpen.value = false;
  highlightedIndex.value = -1;
};

const selectFirstProduct = () => {
  if (validProducts.value.length && highlightedIndex.value >= 0) {
    selectProduct(validProducts.value[highlightedIndex.value]);
  } else if (validProducts.value.length) {
    selectProduct(validProducts.value[0]);
  }
};

const moveHighlight = (direction: number) => {
  if (!validProducts.value.length) return;

  const maxIndex = validProducts.value.length - 1;
  let newIndex = highlightedIndex.value + direction;

  if (newIndex < 0) newIndex = maxIndex;
  if (newIndex > maxIndex) newIndex = 0;

  highlightedIndex.value = newIndex;

  const dropdown = document.querySelector('.max-h-60');
  const highlightedElement = dropdown?.children[highlightedIndex.value] as HTMLElement;
  if (highlightedElement) {
    highlightedElement.scrollIntoView({ block: 'nearest' });
  }
};

const closeDropdown = () => {
  isOpen.value = false;
  highlightedIndex.value = -1;
  if (selectedProduct.value) {
    emit('update:modelValue', selectedProduct.value.name);
  }
};

const clearSelection = () => {
  selectedProduct.value = null;
  emit('update:modelValue', '');
  emit('select', null);
  
  const inputEvent = createInputEvent('');
  emit('change', inputEvent);
  
  products.value = [];
  hasSearched.value = false;
  isOpen.value = false;
  highlightedIndex.value = -1;
};

const reset = () => {
  clearSelection();
};

const handleClickOutside = (event: Event) => {
  const target = event.target as Element;
  if (!target.closest('.relative')) {
    closeDropdown();
  }
};

onMounted(async () => {
  document.addEventListener('click', handleClickOutside);
  if (props.initialId) {
    try {
      const response = await ProductServices.getProductById(props.initialId);
      console.log('Initial product response:', response); // Debug initialId response
      if (response.product && response.product.name && typeof response.product.id === 'number') {
        selectedProduct.value = response.product;
        emit('update:modelValue', response.product.name);
        emit('select', response.product);
      }
    } catch (e) {
      console.error('Error loading product by initialId:', e);
    }
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  debouncedSearch.clear();
});

defineExpose({ reset });
</script>

<style scoped>
.transition-all {
  transition: all 0.2s ease-in-out;
}

.max-h-60 {
  max-height: 15rem;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e0 #f7fafc;
}

.max-h-60::-webkit-scrollbar {
  width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
  background: #f7fafc;
}

.max-h-60::-webkit-scrollbar-thumb {
  background: #cbd5e0;
  border-radius: 3px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
  background: #a0aec0;
}
</style>