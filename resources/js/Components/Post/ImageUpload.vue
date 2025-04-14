<template>
    <div>
        <label class="form-label">Post Image</label>
        <input type="file" class="form-control" @change="handleFileChange" accept="image/*" />
        <div v-if="preview" class="mt-2">
            <img :src="preview" class="img-thumbnail" width="200" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    image: File,
    existingImage: String,
});
const emit = defineEmits(['update:image']);

const preview = ref(props.existingImage ? `/${props.existingImage}` : '');

function handleFileChange(e) {
    const file = e.target.files[0];
    if (file) {
        emit('update:image', file);
        preview.value = URL.createObjectURL(file);
    }
}
</script>
