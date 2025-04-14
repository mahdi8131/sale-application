<template>
    <form @submit.prevent="submitPost" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Title</label>
            <input v-model="form.title" type="text" class="form-control" placeholder="Post Title" required />
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea v-model="form.content" class="form-control" rows="5" placeholder="Write your post..."
                required></textarea>
        </div>

        <div class="form-group">
            <label for="visibility">Visibility</label>
            <select v-model="form.visibility" class="form-control">
                <option value="public">Public</option>
                <option value="private">Private</option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Upload Image</label>
            <input type="file" @change="handleImage" class="form-control" />
        </div>

        <div class="form-group">
            <label for="tags">Tags</label>
            <input v-model="form.tags" type="text" class="form-control" placeholder="Enter tags (comma separated)" />
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';

export default {
    data() {
        return {
            form: useForm({
                title: '',
                content: '',
                visibility: 'public',
                image: null,
                tags: ''
            })
        };
    },
    methods: {
        handleImage(event) {
            this.form.image = event.target.files[0];
        },
        submitPost() {
            this.form.post('/posts', {
                forceFormData: true,
                onSuccess: () => {
                    this.$toast.success('Post created successfully!');
                },
                onError: () => {
                    this.$toast.error('There was an error creating the post.');
                }
            });
        }
    }
};
</script>