<template>
    <RadioGroup v-model="selectedValue" orientation="horizontal" class="flex items-center space-x-4" @update:model-value="handleSelection">
        <div v-for="payment in typePayments" :key="payment.id" class="flex items-center space-x-2">
            <RadioGroupItem :id="`option-${payment.id}`" :value="payment.id.toString()" />
            <Label :for="`option-${payment.id}`">{{ payment.name }}</Label>
        </div>
    </RadioGroup>
</template>

<script setup lang="ts">
import Label from '@/components/ui/label/Label.vue';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { TypePaymens } from '@/interface/ComboBox';
import { comboBoxServices } from '@/services/comboBoxServices';
import { onMounted, ref, watch } from 'vue';

const selectedValue = ref<string>('');
const typePayments = ref<TypePaymens[]>([]);

const props = defineProps<{
    reset: boolean;
}>();

onMounted(async () => {
    try {
        typePayments.value = await comboBoxServices.getTypePayment();
    } catch (error) {
        console.error('Error cargando métodos de pago:', error);
    }
});

const emit = defineEmits<{
    (e: 'emit_type_payment', type: TypePaymens | null): void;
}>();

watch(
    () => props.reset,
    (newVal) => {
        if (newVal) {
            selectedValue.value = '';
        }
    },
);

const handleSelection = (value: string) => {
    const selectedPayment = typePayments.value.find((payment) => payment.id.toString() === value);
    emit('emit_type_payment', selectedPayment || null);
};
</script>

<style scoped></style>
