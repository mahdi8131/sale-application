<template>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Tag Management</h2>
            <Link href="/TagSavePage" class="btn btn-primary">+ Add New Tag</Link>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(tag, index) in tags" :key="tag.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ tag.name }}</td>
                    <td>
                        <Link :href="`/TagSavePage?id=${tag.id}`" class="btn btn-sm btn-warning me-2">Edit</Link>
                        <button @click="deleteTag(tag.id)" class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <tr v-if="tags.length === 0">
                    <td colspan="3" class="text-center text-muted">No tags found.</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup>
import { router, usePage, Link } from '@inertiajs/vue3'

const tags = usePage().props.tags

function deleteTag(id) {
    if (confirm('Are you sure you want to delete this tag?')) {
        router.get(`/TagDelete/${id}`)
    }
}
</script>