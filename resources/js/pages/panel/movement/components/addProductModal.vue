<template>
  <Dialog :open="modal" @update:open="emit('emit-close', false)">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>Agregar Producto</DialogTitle>
        <DialogDescription class="sr-only">
          Formulario para agregar un nuevo producto al movimiento, incluyendo selección de producto, cantidad, lote, fecha de vencimiento y precios.
        </DialogDescription>
      </DialogHeader>

      <!-- Form to Add Product -->
      <form @submit.prevent="onAddProduct" class="space-y-4">
        <FormField v-slot="{ componentField }" name="product">
          <FormItem>
            <FormLabel>Producto *</FormLabel>
            <FormControl>
              <ProductCombobox v-bind="componentField" @select="onProductSelect" :initialId="null" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <!-- Type selection buttons -->
        <div v-if="selectedProduct" class="flex gap-2 mt-2">
          <Button
            type="button"
            variant="outline"
            size="sm"
            :class="{ 'bg-gray-200 dark:bg-gray-700': selectedType === 'Box' }"
            @click="selectedType = 'Box'"
          >
            Cajas
          </Button>
          <Button
            v-if="selectedProduct?.state_fraction"
            type="button"
            variant="outline"
            size="sm"
            :class="{ 'bg-gray-200 dark:bg-gray-700': selectedType === 'Fraction' }"
            @click="selectedType = 'Fraction'"
          >
            Fracciones
          </Button>
          <Button
            v-if="selectedProduct?.state_fraction"
            type="button"
            variant="outline"
            size="sm"
            :class="{ 'bg-gray-200 dark:bg-gray-700': selectedType === 'Both' }"
            @click="selectedType = 'Both'"
          >
            Ambos
          </Button>
        </div>

        <!-- Quantity inputs -->
        <div v-if="selectedProduct" class="space-y-4">
          <FormField v-if="selectedType === 'Box' || selectedType === 'Both'" v-slot="{ componentField }" name="boxes">
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
            v-if="selectedProduct?.state_fraction && (selectedType === 'Fraction' || selectedType === 'Both')"
            v-slot="{ componentField }"
            name="fractions"
          >
            <FormItem>
              <FormLabel>Fracciones (Máx {{ selectedProduct?.fraction }})</FormLabel>
              <FormControl>
                <Input
                  type="number"
                  v-bind="componentField"
                  :max="selectedProduct?.fraction"
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
          <Button type="submit">Guardar</Button>
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
import ProductCombobox from '@/components/Inputs/ProductCombobox.vue';
import { toTypedSchema } from '@vee-validate/zod';
import { useForm } from 'vee-validate';
import * as z from 'zod';
import { ref } from 'vue';
import { ProductMovementServices, ProductMovement } from '@/services/productMovementService';
import { ProductResource } from '@/pages/panel/product/interface/Product';

const props = defineProps<{
  modal: boolean;
  movementId: number;
}>();

const emit = defineEmits<{
  (e: 'emit-close', open: boolean): void;
  (e: 'add-product', product: ProductMovement): void;
}>();

const selectedProduct = ref<ProductResource | null>(null);
const selectedType = ref<string>('Box');

const formSchema = toTypedSchema(
  z.object({
    product: z.string().min(1, 'Seleccione un producto'),
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
    if (selectedType.value === 'Box') {
      if (!data.boxes || data.boxes < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['boxes'],
          message: 'Se requiere al menos 1 caja cuando se selecciona "Cajas"',
        });
      }
    } else if (selectedType.value === 'Fraction') {
      if (!data.fractions || data.fractions < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['fractions'],
          message: 'Se requiere al menos 1 fracción cuando se selecciona "Fracciones"',
        });
      }
    } else if (selectedType.value === 'Both') {
      if (!data.boxes || data.boxes < 1) {
        ctx.addIssue({
          code: z.ZodIssueCode.custom,
          path: ['boxes'],
          message: 'Se requiere al menos 1 caja cuando se selecciona "Ambos"',
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

const onProductSelect = (product: ProductResource | null) => {
  selectedProduct.value = product;
  selectedType.value = 'Box';
  if (product) {
    setFieldValue('product', product.name);
    setFieldValue('boxes', 0);
    setFieldValue('fractions', 0);
  } else {
    setFieldValue('product', '');
    setFieldValue('boxes', 0);
    setFieldValue('fractions', 0);
  }
};

const handleBoxesInput = (event: Event) => {
  const input = event.target as HTMLInputElement;
  const boxes = parseInt(input.value) || 0;
  setFieldValue('boxes', Math.max(0, boxes));
  updateUnitPrice();
};

const handleFractionsInput = (event: Event) => {
  if (!selectedProduct.value) return;

  const input = event.target as HTMLInputElement;
  let fractions = parseInt(input.value) || 0;
  const maxFractions = selectedProduct.value.fraction;

  if (selectedType.value === 'Both' && fractions > maxFractions) {
    const extraBoxes = Math.floor(fractions / maxFractions);
    fractions = fractions % maxFractions;
    setFieldValue('boxes', (values.boxes || 0) + extraBoxes);
    setFieldValue('fractions', fractions);
  } else if (fractions > maxFractions) {
    setFieldValue('fractions', maxFractions);
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

  if (selectedProduct.value) {
    const boxes = parseFloat(values.boxes) || 0;
    const fractions = parseFloat(values.fractions) || 0;
    const taxFactor = selectedProduct.value.state_tax ? 1.18 : 1;

    if (selectedType.value === 'Box') {
      unitPrice = boxes > 0 ? (totalPrice / boxes) * taxFactor : 0;
    } else if (selectedType.value === 'Fraction') {
      unitPrice = fractions > 0 ? (totalPrice / fractions) * taxFactor : 0;
    } else if (selectedType.value === 'Both') {
      const totalUnits = boxes + fractions / (selectedProduct.value.fraction || 1);
      unitPrice = totalUnits > 0 ? (totalPrice / totalUnits) * taxFactor : 0;
    }
  }

  setFieldValue('unit_price', unitPrice > 0 ? Number(unitPrice.toFixed(2)) : 0);
};

const onAddProduct = handleSubmit(async (values) => {
  if (!selectedProduct.value) return;

  const requestData = {
    product_id: selectedProduct.value.id,
    boxes: values.boxes || 0,
    fractions:
      selectedProduct.value.state_fraction && (selectedType.value === 'Fraction' || selectedType.value === 'Both')
        ? values.fractions || 0
        : 0,
    type: selectedType.value,
    batch: values.batch,
    expiry_date: values.expiry_date,
    unit_price: values.unit_price,
    total_price: values.total_price,
    movement_id: props.movementId,
  };

  try {
    const response = await ProductMovementServices.storeProductMovement(requestData);
    const newProduct: ProductMovement = {
      id: response.data.id,
      productId: selectedProduct.value.id,
      quantity: requestData.boxes,
      fractionQuantity: requestData.fractions,
      unitPrice: requestData.unit_price.toFixed(2),
      unitPriceEx: requestData.unit_price.toFixed(2),
      fractionPrice: (requestData.unit_price / (selectedProduct.value.fraction || 1)).toFixed(2),
      totalPrice: requestData.total_price.toFixed(2),
      labName: selectedProduct.value.laboratory,
      productName: selectedProduct.value.name,
      unitPrices: `${requestData.unit_price.toFixed(2)} - ${(requestData.unit_price / (selectedProduct.value.fraction || 1)).toFixed(2)}`,
      batch: requestData.batch,
      expiryDate: requestData.expiry_date,
      expiryDateDisplay: new Date(requestData.expiry_date)
        .toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' })
        .split('/')
        .reverse()
        .join('-'),
      movementId: props.movementId,
      quantityStatus: requestData.type === 'Box' ? 1 : requestData.type === 'Fraction' ? 0 : 2,
      quantityType: requestData.type,
      totalQuantity: (requestData.boxes + requestData.fractions).toString(),
      generalPrice: `${requestData.unit_price.toFixed(2)} - ${(requestData.unit_price / (selectedProduct.value.fraction || 1)).toFixed(2)}`,
      status: 1,
    };

    emit('add-product', newProduct);
    resetForm();
    selectedProduct.value = null;
    selectedType.value = 'Box';
    emit('emit-close', false);
  } catch (error) {
    console.error('Error al agregar el movimiento de producto:', error);
  }
});
</script>