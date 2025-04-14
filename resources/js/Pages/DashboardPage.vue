<template>
  <SideNavLayout>
    <div class="p-4">
      <div class="container">
        <h1 class="mb-4">Welcome to the Blog!</h1>
        <p>This is a simple blogging platform with real-time features.</p>
      </div>
    </div>
  </SideNavLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import SideNavLayout from '../Layout/SideNavLayout.vue';
import { ref } from 'vue';
// Vue router used for redirection

const props = defineProps({
  user: Object,
  stats: Object
});


</script>