<template>
    <RadioGroup v-model="selectedValue" orientation="horizontal" class="flex items-center space-x-4" @update:model-value="handleSelection">
        <div class="flex items-center space-x-2">
            <RadioGroupItem id="option-one" value="1" />
            <Label for="option-one">Factura</Label>
        </div>
        <div class="flex items-center space-x-2">
            <RadioGroupItem id="option-two" value="2" />
            <Label for="option-two">Boleta</Label>
        </div>
        <div class="flex items-center space-x-2">
            <RadioGroupItem id="option-tree" value="3" />
            <Label for="option-tree">Tikcket</Label>
        </div>
    </RadioGroup>
</template>

<script setup lang="ts">
import Label from '@/components/ui/label/Label.vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { TypeVoucher } from '@/interface/ComboBox';
import { ref, watch } from 'vue';

const selectedValue = ref<string>(''); // Para manejar el valor seleccionado del RadioGroup

const emit = defineEmits<{
    (e: 'emit_type_voucher', type: TypeVoucher | null): void;
}>();

const props = defineProps<{
    reset: boolean;
}>();

const handleSelection = (value: string) => {
    const paymentMap: Record<string, TypeVoucher> = {
        '1': { id: 1, name: 'Factura' },
        '2': { id: 2, name: 'Boleta' },
        '3': { id: 3, name: 'Tikcket' },
    };

    if (value in paymentMap) {
        emit('emit_type_voucher', paymentMap[value]);
    } else {
        emit('emit_type_voucher', null);
    }
};

watch(
    () => props.reset,
    (newVal) => {
        if (newVal) {
            selectedValue.value = ''; // Resetea el valor seleccionado
        }
    },
);
</script>

<style scoped></style>
