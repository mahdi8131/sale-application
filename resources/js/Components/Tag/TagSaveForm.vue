<template>
    <div class="container mt-4">
        <h2 class="mb-3">{{ tag ? 'Edit Tag' : 'Create Tag' }}</h2>
        <form @submit.prevent="submit">
            <div class="mb-3">
                <label class="form-label">Tag Name</label>
                <input v-model="form.name" type="text" class="form-control" placeholder="Enter tag name" required />
            </div>
            <button type="submit" class="btn btn-success">{{ tag ? 'Update' : 'Create' }}</button>
            <Link href="/TagPage" class="btn btn-secondary ms-2">Cancel</Link>
        </form>
    </div>
</template>

<script setup>
import { reactive } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'

const tag = usePage().props.tag || null

const form = reactive({
    id: tag?.id,
    name: tag?.name || '',
})

function submit() {
    if (form.id) {
        router.post('/TagUpdate', form)
    } else {
        router.post('/CreateTag', form)
    }
}
</script>