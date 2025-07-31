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

                <!-- genres -->
                <select v-model="selectedGenre" class="form-select w-25">
                    <option :value="null">All Genres</option>
                    <option
                        v-for="genre in allGenres"
                        :key="genre.id"
                        :value="genre.slug"
                    >
                        {{ genre.name }}
                    </option>
                </select>

                <!-- Search Input -->
                <div class="p-3 w-25">
                    <input
                        class="form-control me-2"
                        type="search"
                        placeholder="Search by title or director"
                       v-model="search"
                    />
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
   <div class="p-1 p-md-2 p-lg-5">
    <slot></slot>
</div>

</template>
<script setup>
import { Link, router, usePage } from "@inertiajs/vue3";
import { computed, ref, watch } from "vue";
import { useFilmFilters } from "../composables/useFilmFilters";


const page = usePage();

const emit = defineEmits(["update:search", "genreSelected"]);
// const genre = computed(() => page.props.genre );
const {search,allGenres, filterByGenre} = useFilmFilters();

// function to filter by genre
const selectedGenre = ref( page.props.genre || null);

watch(selectedGenre,() => {
    // load all the films if selected genres is all
    if(selectedGenre.value == null) {
        router.get('/');
        return;
    }
    router.get(`/film/genres/${selectedGenre.value}`);
});
</script>
