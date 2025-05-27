<template>
    <Combobox v-model="value" by="id" @update:model-value="handleSelection" class="w-full">
        <ComboboxAnchor as-child>
            <ComboboxTrigger as-child>
                <Button variant="outline" class="justify-between">
                    {{ value ? value.name : 'Seleccionar doctor' }}
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </ComboboxTrigger>
        </ComboboxAnchor>
        <ComboboxList>
            <div class="relative w-full max-w-sm items-center">
                <ComboboxInput
                    class="h-10 rounded-none border-0 border-b pl-9 focus-visible:ring-0"
                    placeholder="Buscar doctor..."
                    @update:model-value="debouncedSearch"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-3">
                    <Search class="size-4 text-muted-foreground" />
                </span>
            </div>
            <ComboboxEmpty>No se encontraron doctores.</ComboboxEmpty>
            <ComboboxGroup>
                <ComboboxItem v-for="doctor in comboBoxDoctorData" :key="doctor.id" :value="doctor">
                    {{ doctor.name }}
                </ComboboxItem>
            </ComboboxGroup>
        </ComboboxList>
    </Combobox>
</template>

<script setup lang="ts">
import { useComboBox } from '@/composables/useComboBox';
import { comboBoxDoctor } from '@/interface/ComboBox';
import debounce from 'debounce';
import { ChevronsUpDown, Search } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';
import Button from '../ui/button/Button.vue';
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList, ComboboxTrigger } from '../ui/combobox';

const { loadingComboBoxDoctor, comboBoxDoctorData } = useComboBox();
const value = ref<comboBoxDoctor | null>(null);
const searchText = ref('');

const emit = defineEmits<{
    (e: 'emit_doctor', doctor: comboBoxDoctor | null): void;
}>();
const handleSelection = (selectedValue: any) => {
    if (selectedValue && typeof selectedValue === 'object' && 'name' in selectedValue) {
        emit('emit_doctor', selectedValue as comboBoxDoctor);
    } else {
        emit('emit_doctor', null);
    }
};

const debouncedSearch = debounce((texto: string) => {
    searchText.value = texto;
    loadingComboBoxDoctor(texto);
}, 400);

onMounted(() => {
    loadingComboBoxDoctor('');
});
</script>

<style scoped></style>
