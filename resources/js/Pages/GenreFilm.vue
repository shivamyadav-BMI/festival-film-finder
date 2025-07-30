<template>
    <AppLayout :genres="allGenres" :genre="selectedGenre">
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
                <FilterDropDown />
            </div>
        </div>

        <FilmCard :films="films" />

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
import { WhenVisible } from "@inertiajs/vue3";

const {
    films,
    allGenres,
    search,
    sort_by,
    reachedEnd,
    whenVisibleParams,
    loading,
    selectedGenre,
} = useFilmFilters(false);
</script>
