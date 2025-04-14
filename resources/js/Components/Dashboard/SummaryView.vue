<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Welcome, {{ user.username }}</h1>

    <!-- লগআউট বাটন -->
    <Link href="/user-logout" class="side-bar-item">
    <span class="side-bar-item-icon"><i class="fa fa-sign-out-alt text-green" /></span>
    <span class="side-bar-item-caption">Logout</span>
    </Link>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Total Posts</p>
        <p class="text-xl font-bold">{{ stats.posts }}</p>
      </div>
      <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Draft Posts</p>
        <p class="text-xl font-bold">{{ stats.drafts }}</p>
      </div>
      <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Comments</p>
        <p class="text-xl font-bold">{{ stats.comments }}</p>
      </div>
      <div class="bg-white shadow rounded p-4 text-center">
        <p class="text-gray-500">Bookmarks</p>
        <p class="text-xl font-bold">{{ stats.bookmarks }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue';
// Vue router used for redirection

const props = defineProps({
  user: Object,
  stats: Object
});


</script>