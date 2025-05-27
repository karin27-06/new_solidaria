<template>
    <div class="flex flex-row items-start gap-2">
        <div class="flex flex-1 items-center justify-end gap-2">
            <SidebarTrigger class="-ml-1" />
            <Input class="m-0" id="search" type="text" placeholder="Buscar producto" v-model="search" @keyup="handleKeyUp" />
            <Button type="button" variant="outline" size="icon" @click="handleSearch">
                <Search class="h-2 w-2" />
            </Button>
            <Button type="button" variant="outline" size="icon" @click="handleClear">
                <Trash2 class="h-2 w-2" />
            </Button>
        </div>
        <div class="flex flex-1 items-center justify-end gap-2">
            <Button type="button" variant="outline"> Ventas usuario </Button>
            <Button type="button" variant="default">Cerra caja </Button>
        </div>
    </div>
</template>
<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { Search, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const emits = defineEmits<{
    (e: 'search', value: string): void;
    (e: 'deleteResult'): void;
}>();

const search = ref<string>('');
const handleSearch = () => {
    emits('search', search.value);
};

const handleKeyUp = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        handleSearch();
    }
};
const handleClear = () => {
    emits('deleteResult');
};
</script>
<style scoped></style>
