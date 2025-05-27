<template>
  <Dialog :open="modal" @update:open="emit('emit-close', false)">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Editar Producto</DialogTitle>
        <DialogDescription class="sr-only">
          Formulario para editar los detalles de un producto en el movimiento, incluyendo cantidad, lote, fecha de vencimiento y precios.
        </DialogDescription>
      </DialogHeader>

      <!-- Form to Edit Product -->
      <form @submit.prevent="onSubmit" class="space-y-4">
        <!-- Resto del formulario permanece igual -->
        <FormField v-slot="{ componentField }" name="product">
          <FormItem>
            <FormLabel>Producto</FormLabel>
            <FormControl>
              <Input v-bind="componentField" :value="productToEdit?.productName" readonly />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <!-- Quantity inputs based on quantityType -->
        <div class="space-y-4">
          <FormField
            v-if="productToEdit?.quantityType === 'Box' || productToEdit?.quantityType === 'Both'"
            v-slot="{ componentField }"
            name="boxes"
          >
            <FormItem>
              <FormLabel>Cajas</FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  min="0"
                  @input="handleBoxesInput"
                  placeholder="Ingrese el número de cajas"
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>

          <FormField
            v-if="productToEdit?.quantityType === 'Fraction' || productToEdit?.quantityType === 'Both'"
            v-slot="{ componentField }"
            name="fractions"
          >
            <FormItem>
              <FormLabel>Fracciones (Máx {{ maxFractions }})</FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  :max="maxFractions"
                  min="0"
                  @input="handleFractionsInput"
                  placeholder="0"
                />
              </FormControl>
              <FormMessage />
            </FormItem>
          </FormField>
        </div>

        <FormField v-slot="{ componentField }" name="batch">
          <FormItem>
            <FormLabel>Lote *</FormLabel>
            <FormControl>
              <Input v-bind="componentField" placeholder="Ingrese el número de lote" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="expiry_date">
          <FormItem>
            <FormLabel>Fecha Vencimiento *</FormLabel>
            <FormControl>
              <Input type="date" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="total_price">
          <FormItem>
            <FormLabel>Precio Total *</FormLabel>
            <FormControl>
              <Input
                type="number"
                step="0.01"
                v-bind="componentField"
                placeholder="Ingrese el precio total"
                @input="updateUnitPrice"
              />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <FormField v-slot="{ componentField }" name="unit_price">
          <FormItem>
            <FormLabel>Precio Unitario *</FormLabel>
            <FormControl>
              <Input type="number" step="0.01" v-bind="componentField" readonly />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <!-- Footer Actions -->
        <div class="flex justify-end gap-3">
          <Button type="button" variant="outline" @click="emit('emit-close', false)">Cancelar</Button>
          <Button type="submit">Actualizar</Button>
        </div>
      </form>
    </DialogContent>
  </Dialog>
</template>

<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
import { Input } from '@/components/ui/input';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import * as z from 'zod';
import { ref, watch, computed } from 'vue';
import { ProductMovementServices, ProductMovement } from '@/services/productMovementService';

// Formatear la fecha al formato yyyy-MM-dd
const formatDateForInput = (dateString: string) => {
  if (!dateString) return '';
  try {
    // Si la fecha está en formato ISO (con 'T'), tomar solo la parte de la fecha
    if (dateString.includes('T')) {
      return dateString.split('T')[0];
    }
    return dateString;
  } catch (e) {
    console.error('Error formatting date:', e);
    return '';
  }
};

const props = defineProps<{
  modal: boolean;
  movementId: number;
  productToEdit: ProductMovement['data'] | null;
}>();

const emit = defineEmits<{
  (e: 'emit-close', open: boolean): void;
  (e: 'update-product', product: ProductMovement['data']): void;
}>();

const maxFractions = computed(() => {
  return props.productToEdit?.fractionQuantity || 1;
});

const formSchema = toTypedSchema(
  z.object({
    product: z.string().min(1, 'El producto es requerido'),
    boxes: z
      .number({ message: 'El número de cajas debe ser un número' })
      .min(0, 'El número de cajas debe ser al menos 0')
      .optional(),
    fractions: z
      .number({ message: 'El número de fracciones debe ser un número' })
      .min(0, 'El número de fracciones debe ser al menos 0')
      .optional(),
    batch: z.string().min(1, 'El lote es requerido'),
    expiry_date: z.string().min(1, 'La fecha de vencimiento es requerida'),
    total_price: z.number({ message: 'El precio total debe ser un número' }).min(0, 'El precio total debe ser al menos 0'),
    unit_price: z.number({ message: 'El precio unitario debe ser un número' }).min(0, 'El precio unitario debe ser al menos 0'),
  }).superRefine((data, ctx) => {
    if (props.productToEdit?.quantityType === 'Box') {
      if (!data.boxes || data.boxes < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['boxes'],
          message: 'Se requiere al menos 1 caja cuando el tipo es "Cajas"',
        });
      }
    } else if (props.productToEdit?.quantityType === 'Fraction') {
      if (!data.fractions || data.fractions < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['fractions'],
          message: 'Se requiere al menos 1 fracción cuando el tipo es "Fracciones"',
        });
      }
    } else if (props.productToEdit?.quantityType === 'Both') {
      if (!data.boxes || data.boxes < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['boxes'],
          message: 'Se requiere al menos 1 caja cuando el tipo es "Ambos"',
        });
      }
    }
  })
);

const { handleSubmit, setFieldValue, resetForm, values } = useForm({
  validationSchema: formSchema,
  initialValues: {
    product: '',
    boxes: 0,
    fractions: 0,
    batch: '',
    expiry_date: '',
    total_price: 0,
    unit_price: 0,
  },
});

watch(
  () => props.productToEdit,
  (product) => {
    if (product) {
      setFieldValue('product', product.productName);
      setFieldValue('boxes', product.quantity);
      setFieldValue('fractions', product.fractionQuantity);
      setFieldValue('batch', product.batch);
      setFieldValue('expiry_date', formatDateForInput(product.expiryDate)); // Formatear la fecha
      setFieldValue('total_price', parseFloat(product.totalPrice));
      setFieldValue('unit_price', parseFloat(product.unitPrice));
    } else {
      resetForm();
    }
  },
  { immediate: true }
);

const handleBoxesInput = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const boxes = parseInt(input.value) || 0;
  setFieldValue('boxes', Math.max(0, boxes));
  updateUnitPrice();
};

const handleFractionsInput = (event: Event) => {
  if (!props.productToEdit) return;

  const input = event.target as HTMLInputElement;
  let fractions = parseInt(input.value) || 0;

  if (props.productToEdit.quantityType === 'Both' && fractions > maxFractions.value) {
    const extraBoxes = Math.floor(fractions / maxFractions.value);
    fractions = fractions % maxFractions.value;
    setFieldValue('boxes', (values.boxes || 0) + extraBoxes);
    setFieldValue('fractions', fractions);
  } else if (fractions > maxFractions.value) {
    setFieldValue('fractions', maxFractions.value);
  } else if (fractions < 0) {
    setFieldValue('fractions', 0);
  } else {
    setFieldValue('fractions', fractions);
  }

  updateUnitPrice();
};

const updateUnitPrice = () => {
  const totalPrice = parseFloat(values.total_price) || 0;
  let unitPrice = 0;

  if (props.productToEdit) {
    const boxes = parseFloat(values.boxes) || 0;
    const fractions = parseFloat(values.fractions) || 0;
    const taxFactor = props.productToEdit.status ? 1.18 : 1;

    if (props.productToEdit.quantityType === 'Box') {
      unitPrice = boxes > 0 ? (totalPrice / boxes) * taxFactor : 0;
    } else if (props.productToEdit.quantityType === 'Fraction') {
      unitPrice = fractions > 0 ? (totalPrice / fractions) * taxFactor : 0;
    } else if (props.productToEdit.quantityType === 'Both') {
      const totalUnits = boxes + fractions / maxFractions.value;
      unitPrice = totalUnits > 0 ? (totalPrice / totalUnits) * taxFactor : 0;
    }
  }

  setFieldValue('unit_price', unitPrice > 0 ? Number(unitPrice.toFixed(2)) : 0);
};

const onSubmit = handleSubmit(async (values) => {
  if (!props.productToEdit) return;

  const requestData = {
    product_id: props.productToEdit.productId,
    boxes: values.boxes || 0,
    fractions: props.productToEdit.quantityType === 'Fraction' || props.productToEdit.quantityType === 'Both' ? values.fractions || 0 : 0,
    type: props.productToEdit.quantityType,
    batch: values.batch,
    expiry_date: values.expiry_date,
    unit_price: values.unit_price,
    total_price: values.total_price,
    movement_id: props.movementId,
  };

  try {
    const response = await ProductMovementServices.updateProductMovement(props.productToEdit.id, requestData);
    const updatedProduct: ProductMovement['data'] = {
      ...props.productToEdit,
      quantity: requestData.boxes,
      fractionQuantity: requestData.fractions,
      unitPrice: requestData.unit_price.toFixed(2),
      unitPriceEx: requestData.unit_price.toFixed(2),
      fractionPrice: (requestData.unit_price / maxFractions.value).toFixed(2),
      totalPrice: requestData.total_price.toFixed(2),
      batch: requestData.batch,
      expiryDate: requestData.expiry_date,
      expiryDateDisplay: new Date(requestData.expiry_date)
        .toLocaleDateString('en-GB', { day: '2-digit', month: '2-digit', year: 'numeric' })
        .split('/')
        .reverse()
        .join('-'),
      totalQuantity: (requestData.boxes + requestData.fractions).toString(),
      generalPrice: `${requestData.unit_price.toFixed(2)} - ${(requestData.unit_price / maxFractions.value).toFixed(2)}`,
    };

    emit('update-product', updatedProduct);
    resetForm();
    emit('emit-close', false);
  } catch (error) {
    console.error('Error actualizando el movimiento de producto:', error);
  }
});
</script>