<template>
    <AppLayout>
        <div class="max-w-6xl mx-auto">
            <div class="my-10 md:px-4 flex justify-between itens-center gap-6">
                <h3 class="md:text-2xl">Genres {{ genre }} Movies</h3>
                <FilterDropDown />
            </div>

            <!-- genres films cards -->
            <div class="md:px-4">
                <div
                    class="grid grid-cols-2  md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-5"
                >
                    <Link
                        v-for="film in films"
                        :key="film.id"
                        :href="`/film/${film.id}`"
                        prefetch="click"
                        cache-for="30s"
                        class="block"
                    >
                        <FilmCard :film="film" />
                    </Link>
                </div>
            </div>

            <!-- no film found based on your search -->
            <div
                v-if="films.length == 0"
                class="flex justify-center w-full items-center h-[70vh]"
            >
                <h2>
                    No result found.
                </h2>
            </div>

            <!-- :key is re render / mounts the when visible component -->

            <WhenVisible
                :key="search + '-' + sort_by + '-' + films.length"
                :always="!reachedEnd"
                :params="whenVisibleParams"
            >
                <template #fallback>
                    <Spinner />
                </template>
            </WhenVisible>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import FilmCard from "@/components/FilmCard.vue";
import { useFilmFilters } from "@/composables/useFilmFilters";
import FilterDropDown from "@/components/FilterDropDown.vue";
import { Link, usePage, WhenVisible } from "@inertiajs/vue3";
import Spinner from "../components/Spinner.vue";
import { computed } from "vue";

const { films, search, sort_by, reachedEnd, whenVisibleParams, genre } =
    useFilmFilters(false);

</script>
