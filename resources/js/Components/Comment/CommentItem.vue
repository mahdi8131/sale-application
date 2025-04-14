<template>
    <div :class="['comment-item', { 'is-reply': comment.parent_id }]">
        <div class="comment-header d-flex justify-content-between">
            <div class="d-flex align-items-center">
                <img :src="comment.user.profile_picture || '/images/default-avatar.png'" alt="User Avatar"
                    class="avatar me-2" />
                <strong>{{ comment.user.username }}</strong>
            </div>
            <span class="text-muted">{{ formatDate(comment.created_at) }}</span>
        </div>
        <p class="comment-body">{{ comment.content }}</p>

        <div class="comment-actions mt-2">
            <button class="btn btn-sm btn-outline-primary me-2" @click="$emit('reply', comment.id)">Reply</button>
            <button v-if="isOwner" class="btn btn-sm btn-outline-danger"
                @click="$emit('delete', comment.id)">Delete</button>
        </div>

        <div v-if="comment.replies && comment.replies.length" class="replies mt-3 ms-4">
            <CommentItem v-for="reply in comment.replies" :key="reply.id" :comment="reply" :post-owner-id="postOwnerId"
                @reply="$emit('reply', $event)" @delete="$emit('delete', $event)" />
        </div>
    </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import CommentItem from '@/Components/Comment/CommentItem.vue';

const props = defineProps({
    comment: Object,
    postOwnerId: Number,
});

const isOwner = computed(() => props.comment.user_id === props.postOwnerId);

function formatDate(dateStr) {
    return new Date(dateStr).toLocaleString();
}
</script>

<style scoped>
.comment-item {
    background: #f8f9fa;
    border: 1px solid #ddd;
    padding: 12px;
    border-radius: 5px;
    margin-bottom: 10px;
}

.comment-item .avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-body {
    margin-top: 5px;
}

.is-reply {
    border-left: 3px solid #007bff;
    padding-left: 10px;
}
</style>