<template>
    <div class="card border-0">
        <img
            :src="film.poster"
            :alt="film.title"
            loading="lazy"
            class="img-fluid w-100 h-50 object-fit-cover"
        />
        <div class="mt-3">
            <div class="d-flex justify-content-between">
                <span>{{ film.title }}</span>
                <span>{{ film.year }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    film: Object,
});
</script>
