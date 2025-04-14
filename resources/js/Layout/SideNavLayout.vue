<template>
    <div>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/">📝 Blog</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center gap-2">

                        <!-- Static links -->
                        <li class="nav-item">
                            <Link href="/DashboardPage" class="nav-link">Home</Link>
                        </li>
                        <li class="nav-item">
                            <Link href="/PostPage" class="nav-link">Posts</Link>
                        </li>

                        <!-- Authenticated-only links -->
                        <template v-if="authUser">
                            <li class="nav-item">
                                <Link href="/TagPage" class="nav-link">Tag</Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/bookmarks" class="nav-link">Bookmarks</Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/notifications" class="nav-link">Notifications</Link>
                            </li>

                            <!-- Dropdown for authenticated user -->
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button"
                                    data-bs-toggle="dropdown">
                                    <img :src="authUser.profile_pic || '/default-user.png'" class="rounded-circle me-2"
                                        width="30" height="30" />
                                    {{ authUser.username }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <Link href="/DashboardPage" class="dropdown-item">Dashboard</Link>
                                    </li>
                                    <li>
                                        <Link href="/ProfilePage" class="dropdown-item">Profile</Link>
                                    </li>
                                    <li>
                                        <Link href="/user-logout" method="get" as="button" class="dropdown-item">Logout
                                        </Link>
                                    </li>
                                </ul>
                            </li>
                        </template>

                        <!-- Guest-only links -->
                        <template v-else>
                            <li class="nav-item">
                                <Link href="/login" class="nav-link">Login</Link>
                            </li>
                            <li class="nav-item">
                                <Link href="/registration" class="nav-link">Register</Link>
                            </li>
                        </template>
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <slot />
        </main>
    </div>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const authUser = page.props.auth?.user
</script>