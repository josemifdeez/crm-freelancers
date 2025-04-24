<script setup>
import { onMounted, ref } from 'vue';

// Define el 'modelValue' que permitirá usar v-model desde fuera
// y 'rows' para configurar la altura inicial
defineProps({
    modelValue: String,
    rows: {
        type: [Number, String],
        default: 3, // Altura por defecto
    }
});

// Define el evento que emitirá para que v-model funcione
defineEmits(['update:modelValue']);

// Referencia al elemento textarea del DOM
const textarea = ref(null);

// Opcional: Enfocar el textarea cuando se monta si tiene el atributo 'autofocus'
onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

// Opcional: Exponer el método focus para poder llamarlo desde el padre si es necesario
defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="textarea"
        :rows="rows"
    ></textarea>
    <!-- Clases eliminadas: dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600 -->
</template>