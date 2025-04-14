<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <Link class="navbar-brand" href="/">Blog</Link>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <Link href="/" class="nav-link">Home</Link>
          </li>
          <li class="nav-item">
            <Link href="/login" class="nav-link">Login</Link>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <slot>

  </slot>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  name: 'Navbar',
  components: {
    Link,
  },
  data() {
    return {
      isAuthenticated: false // Replace with real auth check or prop from layout
    }
  },
  methods: {
    logout() {
      // You can use Inertia visit for logout or trigger a form submit
      this.$inertia.post('/logout');
    }
  }
}
</script>
