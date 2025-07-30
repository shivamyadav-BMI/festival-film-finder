<template>
    <div class="container">
        <div class="row g-4">
            <Link
                :href="`/film/${film.id}`"
                class="col-12 col-sm-6 col-md-4"
                v-for="film in films"
                :key="film.id"
                prefetch="click"
                cache-for="30s"
            >
                <div class="card h-100">
                    <img
                        :src="film.poster"
                        class="card-img-top"
                        :alt="film.title"
                        loading="lazy"
                    />
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ film.title }}</h5>
                        <h5 class="card-title">{{ film.imdb_rating }}</h5>
                    </div>
                </div>
            </Link>
        </div>
        <div v-if="films.length === 0" class="text-center my-5">
            <h4>No results found.</h4>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    films: Array,
});
</script>
