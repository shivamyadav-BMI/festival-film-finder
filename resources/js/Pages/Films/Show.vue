<template>
    <AppLayout>
        <!-- Hero Section -->
        <div class="container">
            <div class="text-center text-sm-start">
                <Link
                    as="button"
                    class="btn btn-outline-primary btn-sm px-2 py-1 fs-7 fs-sm-6 fs-md-6 mb-3"
                    @click="goBack"
                >
                    ← Back to Movies
                </Link>
            </div>
        </div>
        <div class="container">
            <div class="row align-items-cente">
                <div class="col-md-5">
                    <img
                        :src="film.poster"
                        :alt="film.title"
                        class="img-fluid rounded shadow-sm"
                        loading="lazy"
                    />
                </div>
                <div class="col-md-7 mt-4 mt-md-0">
                    <!-- Parent Card -->
                    <div class="py-4">
                        <!-- Title -->
                        <h1 class="display-5 fw-bold">{{ film.title }}</h1>

                        <!-- Plot Summary -->
                        <div class="mt-3 p-3 border rounded bg-light">
                            <p class="lead mb-0">{{ film.plot_summary }}</p>
                        </div>

                        <!-- Details Card -->
                        <div class="mt-3 p-3 border rounded bg-light">
                            <!-- IMDb Rating -->
                            <div class="pb-3 mb-3 border-bottom">
                                <p class="mb-0"  v-if="film.imdb_rating">
                                    <span class="fw-semibold"
                                        >IMDb Rating:</span
                                    >
                                    {{ film.imdb_rating }}
                                </p>
                            </div>

                            <!-- Director -->
                            <div class="pb-3 mb-3 border-bottom" v-if="film.director">
                                <p class="mb-0">
                                    <span class="fw-semibold">Director:</span>
                                    {{ film.director }}
                                </p>
                            </div>

                            <!-- Festival Awards -->
                            <div class="pb-3 mb-3 " v-if="film.festival_awards">
                                <p class="fw-semibold mb-2">Festival Awards:</p>
                                <div v-if="film.festival_awards">
                                    <div
                                        v-for="(
                                            award, index
                                        ) in film.festival_awards.split(',')"
                                        :key="index"
                                        class="d-block mb-3 border-bottom"
                                    >
                                        🏆 {{ award.trim() }}
                                    </div>
                                </div>
                                <p v-else class="text-muted mb-0">N/A</p>
                            </div>

                            <!-- Genres -->
                            <div v-if="film.genres">
                                <h6 class="fw-semibold mb-2">Genres:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <Link
                                        v-for="genre in film.genres"
                                        :key="genre.id"
                                        :href="`/film/genres/${genre.slug}`"
                                        class="badge bg-success text-decoration-none"
                                    >
                                        {{ genre.name }}
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Info Section -->
        <!-- <div class="container pb-5">
            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Festival Awards</h5>
                            <p class="card-text">{{ film.festival_awards || 'No awards listed.' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title text-success">Genres</h5>
                            <p
                                class="card-text d-inline-block me-2"
                                v-for="genre in film.genres"
                                :key="genre.id"
                            >
                                <span class="badge bg-success">{{ genre.name }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
    </AppLayout>
</template>

<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import AppLayout from "../../layouts/AppLayout.vue";
import { computed } from "vue";

const page = usePage();
const film = computed(() => page.props.film);

// go to the previous url
function goBack() {
    window.history.back();
}
</script>
