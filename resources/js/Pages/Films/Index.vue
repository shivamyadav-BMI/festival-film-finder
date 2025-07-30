<template>
    <AppLayout :genres="allGenres">
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
                <FilterDropDown />
            </div>
        </div>

        <!-- testing cards -->

        <!-- films cards -->
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
import { Link, WhenVisible } from "@inertiajs/vue3";
import AppLayout from "../../layouts/AppLayout.vue";
import { useFilmFilters } from "@/composables/useFilmFilters";
import FilterDropDown from "../../components/FilterDropDown.vue";
import FilmCard from "../../components/FilmCard.vue";
import { onMounted, onUnmounted } from "vue";

const {
    films,
    allGenres,
    search,
    sort_by,
    reachedEnd,
    whenVisibleParams,
    loading,
} = useFilmFilters(true); // allow filtering by genre
</script>
