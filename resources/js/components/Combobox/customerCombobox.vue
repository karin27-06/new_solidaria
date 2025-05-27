<template>
    <Combobox v-model="value" by="id" @update:model-value="handleSelection" class="w-full">
        <ComboboxAnchor as-child>
            <ComboboxTrigger as-child>
                <Button variant="outline" class="justify-between">
                    {{ value ? `${value.firstname} ${value.lastname}` : 'Seleccionar cliente' }}
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </ComboboxTrigger>
        </ComboboxAnchor>
        <ComboboxList>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput
                    class="h-10 rounded-none border-0 border-b pl-9 focus-visible:ring-0"
                    placeholder="Buscar cliente..."
                    @update:model-value="debouncedSearch"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <ComboboxEmpty>No se encontraron clientes.</ComboboxEmpty>
            <ComboboxGroup>
                <ComboboxItem v-for="customer in comboBoxCustomerData" :key="customer.id" :value="customer">
                    {{ customer.firstname }} {{ customer.lastname }}
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>

<script setup lang="ts">
import { useComboBox } from '@/composables/useComboBox';
import { ComboBoxCustomer } from '@/interface/ComboBox';
import debounce from 'debounce';
import { ChevronsUpDown, Search } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';
import Button from '../ui/button/Button.vue';
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList, ComboboxTrigger } from '../ui/combobox';

const { loadingComboBoxCustomer, comboBoxCustomerData } = useComboBox();
const value = ref<ComboBoxCustomer | null>(null);
const searchText = ref('');

const props = defineProps<{
    reset: boolean;
}>();

const emit = defineEmits<{
    (e: 'emit_customer', customer: ComboBoxCustomer | null): void;
}>();
const handleSelection = (selectedValue: any) => {
    if (selectedValue && typeof selectedValue === 'object' && 'firstname' in selectedValue && 'lastname' in selectedValue) {
        emit('emit_customer', selectedValue as ComboBoxCustomer);
    } else {
        emit('emit_customer', null);
    }
};

// Debounce la función de búsqueda
const debouncedSearch = debounce((texto: string) => {
    searchText.value = texto;
    loadingComboBoxCustomer(texto);
}, 400);

watch(
    () => props.reset,
    (newVal) => {
        if (newVal) {
            value.value = null;
        }
    },
);

onMounted(() => {
    loadingComboBoxCustomer('');
});
</script>

<style scoped></style>
