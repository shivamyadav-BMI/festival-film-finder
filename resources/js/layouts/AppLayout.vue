<template>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarTogglerDemo01"
                aria-controls="navbarTogglerDemo01"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
                <Link href="/" class="navbar-brand">Festival Film Finder</Link>
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <Link
                            href="/"
                            class="nav-link active"
                            aria-current="page"
                            >Home</Link
                        >
                    </li>
                    <li class="nav-item">
                        <Link href="/about" class="nav-link">About</Link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Search Input -->
    <div class="p-3">
        <input
            class="form-control me-2"
            type="search"
            placeholder="Search by title or director"
            :value="props.search"
            @input="(e) => emit('update:search', e.target.value)"
        />
    </div>

    <!-- Main Content -->
    <div class="p-5">
        <slot></slot>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  search: String,
});

const emit = defineEmits(["update:search"]);
</script>
