<template>
    <div class="container">
        <div class="row g-5">
            <Link
                :href="`/film/${film.id}`"
                class="col-12 col-sm-6 col-md-4 col-lg-3"
                v-for="film in films"
                :key="film.id"
                prefetch="click"
                cache-for="30s"
            >
                <div class="card" >
                    <img
                        :src="film.poster"
                        :alt="film.title"
                        loading="lazy"
                        class="card-img-top"
                    />

                </div>

            </Link>
        </div>
        <div v-if="films.length === 0" class="text-center my-5">
            <h4>No results found.</h4>
        </div>
    </div>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    films: Array,
});
</script>

<style scoped>
.movie_card {
    border: 2;
    /* width: 100%; */
    width: 400px;
    height: 400px;
}

.movie_image {
    object-fit: contain;
    width: 400px;
    height: 400px;
}
</style>
