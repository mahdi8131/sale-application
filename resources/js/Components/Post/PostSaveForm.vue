<template>
    <div class="container mt-5">
        <div class="card shadow-sm p-4">
            <h2 class="mb-4">{{ post?.id ? 'Edit Post' : 'Create Post' }}</h2>
            <form @submit.prevent="submit" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input v-model="form.title" type="text" class="form-control" required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea v-model="form.content" rows="5" class="form-control" required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Visibility</label>
                    <select v-model="form.visibility" class="form-select">
                        <option value="public">Public</option>
                        <option value="private">Private</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tags</label>
                    <select v-model="form.tags" multiple class="form-select">
                        <option v-for="tag in tags" :key="tag.id" :value="tag.id">{{ tag.name }}</option>
                    </select>
                </div>

                <div class="mb-3">
                    <ImageUpload v-model:image="form.image" :existing-image="post?.image" />
                </div>

                <button type="submit" class="btn btn-success w-100">
                    {{ post?.id ? 'Update Post' : 'Create Post' }}
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import ImageUpload from './ImageUpload.vue';

const post = usePage().props.post || null;
const tags = usePage().props.tags || [];

const form = reactive({
    id: post?.id,
    title: post?.title || '',
    content: post?.content || '',
    visibility: post?.visibility || 'public',
    tags: post?.tags?.map(t => t.id) || [],
    image: null,
});

function submit() {
    const formData = new FormData();
    formData.append('title', form.title);
    formData.append('content', form.content);
    formData.append('visibility', form.visibility);
    form.tags.forEach(tag => formData.append('tags[]', tag));
    if (form.image) formData.append('image', form.image);
    if (form.id) {
        formData.append('id', form.id);
        router.post('/update-post', formData);
    } else {
        router.post('/create-post', formData);
    }
}
</script>
