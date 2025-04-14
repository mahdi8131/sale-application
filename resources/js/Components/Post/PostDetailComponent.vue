<template>
    <div class="container py-4">
        <h2 class="fw-bold">{{ post.title }}</h2>
        <!-- <img v-if="post.image" :src="`/storage/${post.image}`" class="img-fluid mb-3" /> -->
        <img v-if="post.image" :src="`/${post.image}`" class="img-fluid mb-3" />

        <p>{{ post.content }}</p>

        <p class="text-muted">
            <strong>Author:</strong> {{ post.user.name }} |
            <strong>Tags:</strong> {{post.tags.map(t => t.name).join(', ')}}
        </p>

        <!-- Comment Form -->
        <div class="mt-4">
            <h4>Add Comment</h4>
            <CommentForm :post-id="post.id" :parent-id="parentId" :comment-body="commentBody" :loading="loading"
                @submit-comment="submitComment" @cancel-reply="cancelReply" />
        </div>

        <!-- Comment List -->
        <div class="mt-5">
            <h4>Comments ({{ countComments(post.comments) }})</h4>

            <div v-if="post.comments.length === 0" class="alert alert-info mt-3">
                No comments yet. Be the first to comment!
            </div>

            <div v-else class="mt-3">
                <CommentItem v-for="comment in post.comments" :key="`comment-${comment.id}`" :comment="comment"
                    :post-owner-id="post.user.id" @reply="setReply" @delete="deleteComment" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';
import CommentForm from '@/Components/Comment/CommentForm.vue';
import CommentItem from '@/Components/Comment/CommentItem.vue';

const props = defineProps({
    post: {
        type: Object,
        required: true
    }
});

const post = reactive(props.post);
const commentBody = ref('');
const loading = ref(false);
const postId = post.id;
const parentId = ref(null);

// Cancel reply
function cancelReply() {
    parentId.value = null;
    commentBody.value = '';
}

// Add comment/reply to the tree
function addCommentToTree(comment) {
    if (comment.parent_id) {
        const parent = findComment(post.comments, comment.parent_id);
        if (parent) {
            if (!parent.replies) parent.replies = [];
            parent.replies.unshift(comment); // Newest first
        }
    } else {
        post.comments.unshift(comment);
    }
}

// Recursive find comment by id
function findComment(comments, id) {
    for (let comment of comments) {
        if (comment.id === id) return comment;
        if (comment.replies) {
            const found = findComment(comment.replies, id);
            if (found) return found;
        }
    }
    return null;
}

// Improved recursive remove comment
function removeCommentRecursive(comments, id) {
    return comments.reduce((acc, comment) => {
        if (comment.id !== id) {
            const newComment = { ...comment };
            if (newComment.replies && newComment.replies.length) {
                newComment.replies = removeCommentRecursive(newComment.replies, id);
            }
            acc.push(newComment);
        }
        return acc;
    }, []);
}

// Submit comment or reply
async function submitComment() {
    if (!commentBody.value.trim()) return;

    loading.value = true;
    try {
        const response = await axios.post('/comments', {
            content: commentBody.value,
            post_id: postId,
            parent_id: parentId.value
        });

        addCommentToTree(response.data.comment);
        commentBody.value = '';
        parentId.value = null;
    } catch (error) {
        console.error('Failed to post comment:', error);
        alert('Failed to post comment. Please try again.');
    } finally {
        loading.value = false;
    }
}

// Delete comment or reply
async function deleteComment(id) {
    if (!confirm('Are you sure you want to delete this comment?')) return;

    try {
        await axios.delete(`/comments/${id}`)
        post.comments = removeCommentRecursive(post.comments, id);
    } catch (error) {
        console.error('Delete failed:', error);
        alert('Failed to delete comment. Please try again.');
    }
}

// Set reply
function setReply(id) {
    parentId.value = id;
    commentBody.value = '';
}

// Count comments recursively
function countComments(comments) {
    return comments.reduce((total, comment) => {
        return total + 1 + (comment.replies ? countComments(comment.replies) : 0);
    }, 0);
}

// Real-time comment via Pusher
onMounted(() => {
    window.Echo.channel(`post.${post.id}`).listen('NewCommentEvent', (e) => {
        addCommentToTree(e.comment)
    })
});
</script>