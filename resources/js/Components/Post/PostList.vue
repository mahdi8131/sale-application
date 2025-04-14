<template>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">My Posts</h2>
            <Link href="/PostSavePage" class="btn btn-primary">+ New Post</Link>
        </div>

        <div class="mb-4">
            <input v-model="search" @input="searchPosts" placeholder="Search..." class="form-control" />
        </div>

        <div v-if="posts.length === 0" class="text-center text-muted">
            No posts found.
        </div>

        <div v-else class="row">
            <div v-for="post in posts" :key="post.id" class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img v-if="post.image" :src="`/${post.image}`" class="card-img-top" alt="Post Image"
                        style="height: 200px; object-fit: cover;" />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ post.title }}</h5>
                        <p class="card-text">
                            {{ post.content.substring(0, 100) }}...
                        </p>
                        <p class="text-muted small mb-2">
                            Tags: {{post.tags.map(t => t.name).join(', ')}}
                        </p>
                        <div class="mt-auto d-flex justify-content-between">
                            <Link :href="`/PostSavePage?id=${post.id}`" class="btn btn-sm btn-outline-primary">
                            Edit
                            </Link>
                            <Link :href="`/post-detail/${post.id}`" class="btn btn-sm btn-outline-info">
                            Post Detail
                            </Link>
                            <button @click="deletePost(post.id)" class="btn btn-sm btn-outline-danger">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link, usePage } from '@inertiajs/vue3'

const posts = ref(usePage().props.posts || [])
const search = ref(usePage().props.search || '')

function searchPosts() {
    router.get('/PostPage', { search: search.value }, { preserveScroll: true })
}

function deletePost(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        router.get(`/delete-post/${id}`)
    }
}
</script>

<style scoped>
.card-title {
    font-size: 1.25rem;
    font-weight: bold;
}
</style>
