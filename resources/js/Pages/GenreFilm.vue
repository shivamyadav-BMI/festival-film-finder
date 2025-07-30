<template>
    <AppLayout
        :genres="allGenres"
        :genre="selectedGenre"
        v-model:search="search"
    >
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
                <div class="dropdown">
                    <a
                        class="btn btn-secondary dropdown-toggle"
                        href="#"
                        role="button"
                        id="dropdownMenuLink"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                    >
                        Filter by
                    </a>
                    <div
                        class="dropdown-menu"
                        aria-labelledby="dropdownMenuLink"
                    >
                        <li
                            role="button"
                            @click="sortBy('asc')"
                            class="dropdown-item"
                        >
                            Low to high (rating)
                        </li>
                        <li
                            role="button"
                            @click="sortBy('desc')"
                            class="dropdown-item"
                        >
                            High to low (rating)
                        </li>
                    </div>
                </div>
            </div>
        </div>

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
                    <div class="card h-100" style="width: 100%">
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
            <!-- show the not found search result  -->
            <div class="" v-if="films.length == 0">
                <h4 class="mx-auto">
                    No search result found for <span>{{ search }}</span>
                </h4>
            </div>
        </div>

        <!-- :key is re render / mounts the when visible component -->
        <WhenVisible
            :buffer="500"
            :key="search + '-' + sort_by"
            :always="!reachedEnd"
            :params="whenVisibleParams"
        >
            <template v-if="loading">
                <div>Loading...</div>
            </template>
        </WhenVisible>
    </AppLayout>
</template>

<script setup>
import { Link, WhenVisible } from "@inertiajs/vue3";
import AppLayout from "../layouts/AppLayout.vue";
import { useFilmFilters } from "@/composables/useFilmFilters";

const {
    films,
    allGenres,
    search,
    sort_by,
    reachedEnd,
    whenVisibleParams,
    loading,
    sortBy,
    selectedGenre
} = useFilmFilters(false); // genre is static in this page, so false
</script>
