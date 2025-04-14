<template>
    <div class="container py-4">
        <!-- Comment Form -->
        <div class="mt-4" v-if="authUser">
            <h4>Add Comment</h4>
            <form @submit.prevent="submitComment">
                <textarea v-model="commentBody" class="form-control mb-2" placeholder="Write your comment..." rows="3"
                    :disabled="loading"></textarea>
                <button type="submit" class="btn btn-primary" :disabled="loading || !commentBody.trim()">
                    {{ loading ? 'Posting...' : 'Post Comment' }}
                </button>
                <button v-if="parentId" @click="cancelReply" type="button" class="btn btn-outline-secondary ms-2">
                    Cancel Reply
                </button>
            </form>
        </div>
        <div v-else class="alert alert-info mt-4">
            Please
            <Link :href="route('login')">login</Link> to comment.
        </div>

        <!-- Comments List -->
        <div class="mt-5">
            <h4>Comments ({{ commentsCount }})</h4>
            <div v-if="loadingComments" class="text-center my-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <div v-else-if="comments.length === 0" class="alert alert-info">
                No comments yet. Be the first to comment!
            </div>
            <CommentItem v-for="comment in comments" :key="comment.id" :comment="comment" :post-owner-id="post.user_id"
                @reply="handleReply" @delete="handleDelete" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import CommentItem from '@/Components/Comment/CommentItem.vue';
import axios from 'axios';

const props = defineProps({
    post: Object,
});

const authUser = usePage().props.auth.user;

const commentBody = ref('');
const parentId = ref(null);
const loading = ref(false);
const comments = ref(props.post.comments || []);
const loadingComments = ref(false);

const commentsCount = computed(() => {
    return comments.value.reduce((count, c) => count + 1 + (c.replies?.length || 0), 0);
});

function findComment(list, id) {
    for (const c of list) {
        if (c.id === id) return c;
        if (c.replies) {
            const found = findComment(c.replies, id);
            if (found) return found;
        }
    }
    return null;
}

function removeComment(list, id) {
    for (let i = 0; i < list.length; i++) {
        if (list[i].id === id) {
            list.splice(i, 1);
            return true;
        }
        if (list[i].replies) {
            if (removeComment(list[i].replies, id)) return true;
        }
    }
    return false;
}

function handleReply(id) {
    parentId.value = id;
    const comment = findComment(comments.value, id);
    if (comment) {
        commentBody.value = `@${comment.user.username} `;
        scrollToForm();
    }
}

function handleDelete(id) {
    if (confirm('Are you sure?')) {
        axios.delete(`/comments/${id}`).then(() => {
            removeComment(comments.value, id);
        });
    }
}

function scrollToForm() {
    setTimeout(() => {
        document.querySelector('textarea')?.scrollIntoView({ behavior: 'smooth' });
    }, 100);
}

function cancelReply() {
    parentId.value = null;
    commentBody.value = '';
}

async function submitComment() {
    if (!commentBody.value.trim()) return;

    loading.value = true;
    try {
        const response = await axios.post('/comments', {
            content: commentBody.value,
            post_id: props.post.id,
            parent_id: parentId.value,
        });

        const newComment = response.data.comment;

        if (parentId.value) {
            const parent = findComment(comments.value, parentId.value);
            if (parent) {
                parent.replies = parent.replies || [];
                parent.replies.unshift(newComment);
            }
        } else {
            comments.value.unshift(newComment);
        }

        commentBody.value = '';
        parentId.value = null;
    } finally {
        loading.value = false;
    }
}

// Real-time comment listening via Echo (Pusher)
onMounted(() => {
    if (typeof window.Echo !== 'undefined') {
        window.Echo.channel(`post.${props.post.id}`).listen('NewCommentEvent', (e) => {
            const comment = e.comment;
            if (comment.parent_id) {
                const parent = findComment(comments.value, comment.parent_id);
                if (parent) {
                    parent.replies = parent.replies || [];
                    parent.replies.unshift(comment);
                }
            } else {
                comments.value.unshift(comment);
            }
        });
    }
});
</script>