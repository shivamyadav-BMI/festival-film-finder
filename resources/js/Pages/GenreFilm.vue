<template>
    <AppLayout
        :genres="allGenres"
        :genre="selectedGenre"
    >
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
                <FilterDropDown />
            </div>
        </div>

        <FilmCard :films="films" />

        <!-- :key is re render / mounts the when visible component -->
        <Visible
            :key="search + '-' + sort_by"
            :always="!reachedEnd"
            :params="whenVisibleParams"
            :loading="loading"
        />
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import FilmCard from "@/components/FilmCard.vue";
import { useFilmFilters } from "@/composables/useFilmFilters";
import Visible from "@/components/Visible.vue";
import FilterDropDown from "@/components/FilterDropDown.vue";

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
