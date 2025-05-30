<!-- src/components/PrintReceiptModal.vue -->
<template>
    <Dialog :open="isOpen" @update:open="handleClose">
        <DialogContent class="sm:max-w-4xl">
            <DialogHeader>
                <DialogTitle>Comprobante de Movimiento</DialogTitle>
                <DialogDescription>
                    Vista previa del comprobante. Puede imprimir o descargar el PDF.
                </DialogDescription>
            </DialogHeader>
            <div class="mt-4">
                <iframe
                    :src="pdfUrl"
                    class="w-full h-[600px] border rounded"
                    frameborder="0"
                ></iframe>
            </div>
            <DialogFooter>
                <Button variant="outline" @click="handleClose">Cerrar</Button>
                <Button @click="printPDF">Imprimir</Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>

<script setup lang="ts">
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import Button from '@/components/ui/button/Button.vue';
import { computed } from 'vue';
import { MovementResource } from '../pages/panel/movement/interface/Movement';

const props = defineProps<{
    movement: MovementResource | null;
}>();

const emit = defineEmits<{
    (e: 'close'): void;
}>();

const isOpen = computed(() => !!props.movement);

const pdfUrl = computed(() => {
    if (!props.movement) return '';
    return `/panel/movements/${props.movement.id}/print`; // Added /panel prefix
});

const handleClose = () => {
    emit('close');
};

const printPDF = () => {
    const iframe = document.querySelector('iframe');
    if (iframe) {
        iframe.contentWindow?.print();
    }
};
</script>

<style scoped>
iframe {
    @apply w-full h-[600px] border rounded;
}
</style>