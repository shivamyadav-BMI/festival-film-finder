<template>
    <AppLayout>
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
                <FilterDropDown />
            </div>
        </div>

        <!-- films cards -->
        <div class="container">
            <h3>Trending Movies</h3>

            <div class="row g-4">
                <div
                    class="col-12 col-sm-6 col-md-4 col-lg-3"
                    v-for="film in films"
                    :key="film.id"
                >
                    <Link
                        :href="`/film/${film.id}`"
                        class="text-decoration-none h-100 d-block"
                        prefetch="click"
                        cache-for="30s"
                    >
                        <FilmCard :film="film" />
                    </Link>
                </div>
            </div>

            <!-- No films found -->
            <div v-if="films.length === 0" class="text-center my-5">
                <h4>
                    No results found for search:
                    <span class="fw-semibold">{{ search }}</span>
                </h4>
            </div>
        </div>

        <!-- :key is re render / mounts the when visible component -->
        <WhenVisible
            :key="search + '-' + sort_by + '-' + films.length"
            :always="!reachedEnd"
            :params="whenVisibleParams"
        >
            <template v-if="loading">
                <div class="text-center py-3">Loading...</div>
            </template>
        </WhenVisible>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import FilmCard from "@/components/FilmCard.vue";
import { useFilmFilters } from "@/composables/useFilmFilters";
import FilterDropDown from "@/components/FilterDropDown.vue";
import { Link, WhenVisible } from "@inertiajs/vue3";

const {
    films,
    search,
    sort_by,
    reachedEnd,
    whenVisibleParams,
    loading,
} = useFilmFilters(false);
</script>
