<template>
    <div class="space-y-2">
        <label for="profile_pic" class="cursor-pointer inline-block">
                <img :src="(preview || currentImage) ?? 'placeholder.png'" class="img-thumbnail" height="50px" width="50px" />
        </label>
        <input @change="imageSelected" type="file" name="profile_pic" id="profile_pic" accept="image/*"
            class="hidden" />
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: File,
    existingImage: String,
});

const emit = defineEmits(['update:modelValue']);

const defaultImage = '/storage/profile_pics/placeholder.png';

const preview = ref(null);

// Show existing image if available
watch(
    () => props.existingImage,
    (value) => {
        if (value) {
            preview.value = value;
        }
    },
    { immediate: true }
);

const imageSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        preview.value = URL.createObjectURL(file);
        emit('update:modelValue', file);
    }
};
</script>