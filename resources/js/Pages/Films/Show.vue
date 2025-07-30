<script setup>
import { usePage } from "@inertiajs/vue3";
import AppLayout from "../../layouts/AppLayout.vue";
import { computed } from "vue";

const page = usePage();
const film = computed(() => page.props.film);
</script>

<template>
    <AppLayout>
        <div class="jumbotron p-3 p-md-5 text-white rounded bg-dark">
            <div class="col-md-6 px-0">
                <img loading="lazy" :src="film.poster" class="card-img-top" :alt="film.title" />
                <h1 class="display-4 font-italic">{{ film.title }}</h1>
                <p class="lead my-3">{{ film.plot_summary }}</p>
                <p class="lead mb-0">
                    <a href="#" class="text-white font-weight-bold"
                        >Rating : {{ film.imdb_rating }}</a
                    >
                </p>
            </div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6">
                <div class="card flex-md-row mb-4 box-shadow h-md-250">
                    <div class="card-body d-flex flex-column align-items-start">
                        <strong class="d-inline-block mb-2 text-primary"
                            >Festival Awards</strong
                        >

                        <p class="card-text mb-auto">
                            {{ film.festival_awards }}
                        </p>
                        <h3 class="mb-0">
                            <span class="text-dark">Director</span>
                        </h3>
                        <p class="card-text mb-auto">{{ film.director }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card flex-md-row mb-4 box-shadow h-md-250">
                    <div class="card-body d-flex flex-column align-items-start">
                        <strong class="d-inline-block mb-2 text-success"
                            >Genres</strong
                        >

                        <p class="card-text mb-auto" v-for="genre in film.genres">
                            <span>{{ genre.name }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<!-- for prefetching the data of the index page (implement it later)  -->
<!-- preftech="mount" || preftech
 cache-for="1m" || cache-for="30s" -->
