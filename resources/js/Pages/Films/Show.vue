<template>
    <AppLayout>
        <div class="flex flex-col lg:flex-row gap-10 my-10 mx-auto max-w-6xl">
            <div class="md:px-4 lg:w-1/3 lg:sticky lg:top-[80px] lg:self-start">
                <div
                    class="w-[85vw] md:w-full md:h-auto lg:h-[70vh] overflow-hidden"
                >
                    <img
                        :src="film.poster"
                        class="w-full h-auto md:h-full object-contain md:object-cover rounded-lg"
                        alt=""
                    />
                </div>
            </div>

            <div class="flex-1 flex flex-col lg:px-3">
                <div class="flex flex-col gap-5">
                    <div class="flex flex-col borde p-5 rounded-xl shadow">
                        <div
                            class="hidden lg:flex justify-between items-center"
                        >
                            <div>
                                <div
                                    class="flex cursor-pointer border-orange-500 text-orange-500 hover:text-white hover:bg-orange-500 items-center text-semibold rounded-full px-2 py-1 border"
                                >
                                    <span class="">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            width="24"
                                            height="24"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="lucide lucide-chevron-left-icon lucide-chevron-left"
                                        >
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                    </span>
                                    <span @click="back">Back to movies</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="my-12 text-3xl font-serif lg:text-balance text-center lg:text-start"
                        >
                            <h3 class="">
                                {{ film.title }}
                            </h3>
                        </div>

                        <div v-if="film.plot_summary">
                            <p class="line-clamp-5">
                                {{ film.plot_summary }}
                            </p>
                        </div>

                        <div class="my-3" v-if="film.director">
                            <h3>Director</h3>
                            <p>{{ film.director }}</p>
                        </div>
                        <div class="my-3" v-if="film.year">
                            <h3>Year</h3>
                            <p>{{ film.year }}</p>
                        </div>
                        <div
                            v-if="
                                film.imdb_rating ||
                                film.rotten_tomatoes_rating ||
                                film.metacritic_rating
                            "
                        >
                            <h3 class="text-lg my-3">Ratings</h3>
                            <div class="flex flex-wrap gap-5">
                                <div
                                    v-if="film.imdb_rating"
                                    class="p-2 rounded-lg w- lg:w-32 flex flex-col items-center gap-3 justify-center shadow border"
                                >
                                    <h3 class="">Imdb</h3>
                                    <p>{{ film.imdb_rating }}</p>
                                </div>
                                <div
                                    v-if="film.rotten_tomatoes_rating"
                                    class="p-2 rounded-lg w- lg:w-32 flex flex-col items-center gap-3 justify-center shadow border"
                                >
                                    <h3 class="text-justify">Rotten</h3>
                                    <p>{{ film.rotten_tomatoes_rating }}</p>
                                </div>
                                <div
                                    v-if="film.metacritic_rating"
                                    class="p-2 rounded-lg w- lg:w-32 flex flex-col items-center gap-3 justify-center shadow border"
                                >
                                    <h3 class="">Metacritic</h3>
                                    <p>{{ film.metacritic_rating }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-if="film.genres">
                            <h3 class="my-3 text-lg">Genres</h3>
                            <div class="flex flex-wrap gap-3">
                                <Link
                                    :href="`/film/genres/${genre.slug}`"
                                    v-for="genre in film.genres"
                                    class="rounded-full text-sm lg:text-md p-2 bg-orange-500 hover:bg-orange-600"
                                >
                                    {{ genre.name }}
                                </Link>
                            </div>
                        </div>

                        <div v-if="film.festival_awards">
                            <h3 class="my-3 text-lg">Festival Awards</h3>
                            <div class="">
                                <div
                                    v-for="award in film.festival_awards.split(
                                        '|'
                                    )"
                                    class="border-t py-3 flex gap-3"
                                >
                                    {{ award }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import AppLayout from "../../layouts/AppLayout.vue";
defineProps({
    film: Object,
});

function back() {
    window.history.back();
}
</script>
