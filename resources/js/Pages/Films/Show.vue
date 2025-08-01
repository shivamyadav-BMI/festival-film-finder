<template>
    <AppLayout>
        <div class="flex flex- max-w-6xl mx-auto">
            <div class="layout-content-container flex flex-col flex-1">
                <div class="@container">
                    <div
                        class="flex flex-col gap-6 px-4 py-10 @[480px]:gap-8 @[864px]:flex-row"
                    >
                        <div
                            class="w-full  lg:w-4/4 xl:w-3/4 md:h-[70vh] h-[50vh] aspect-[3/4] overflow-hidden rounded-xl"
                        >
                            <img
                                :src="film.poster"
                                :alt="film.title"
                                class="w-full h-full object-fit"
                            />
                        </div>
                        <!-- movie details -->
                        <div
                            class="flex flex-col gap-6 @[480px]:min-w-[400px] @[480px]:gap-8"
                        >
                            <div class="flex flex-col gap-5 text-left h-full">
                                <div class="md:flex-1">
                                    <h1
                                        class="text-[#141414] text-4xl font-black leading-tight tracking-[-0.033em] @[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em]"
                                    >
                                        {{ film.title }}
                                    </h1>
                                    <h2
                                        class="mt-5 text-[#141414] text-sm font-normal leading-normal @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal"
                                    >
                                        <div class="space-y-3">
                                            <div class="flex gap-2 flex-wrap">
                                                {{ film.plot_summary }}
                                            </div>
                                        </div>
                                    </h2>
                                    <div v-if="film.year" class="py-5">
                                        <p
                                            class="text-xl font-semibold leading-normal"
                                        >
                                            Year
                                        </p>
                                        <p
                                            class="text-[#141414] text-md font-normal leading-normal"
                                        >
                                            {{ film.year }}
                                        </p>
                                    </div>
                                </div>

                                <!-- This is the second child div we want to push to the bottom -->
                                <div class="">
                                    <div class="space-y-3" v-if="film.director">
                                        <h2 class="text-xl font-semibold">
                                            Director
                                        </h2>
                                        <div class="flex gap-2 flex-wrap">
                                            {{ film.director }}
                                        </div>
                                    </div>

                                    <div
                                        v-if="
                                            film.imdb_rating &&
                                            film.rotten_tomatoes_rating &&
                                            film.metacritic_rating
                                        "
                                        class="space-y-3 my-4"
                                    >
                                        <h2 class="text-xl font-semibold">
                                            Ratings
                                        </h2>
                                        <div
                                            class="flex justify-between gap-5 flex-wrap"
                                        >
                                            <div v-if="film.imdb_rating">
                                                <h3 class="text-md">
                                                    Imdb Rating
                                                    <p>
                                                        {{ film.imdb_rating }}
                                                    </p>
                                                </h3>
                                            </div>
                                            <div
                                                v-if="
                                                    film.rotten_tomatoes_rating
                                                "
                                            >
                                                <h3 class="text-md">
                                                    Imdb Rating
                                                    <p>
                                                        {{
                                                            film.rotten_tomatoes_rating
                                                        }}
                                                    </p>
                                                </h3>
                                            </div>
                                            <div v-if="film.metacritic_rating">
                                                <h3 class="text-md">
                                                    Imdb Rating
                                                    <p>
                                                        {{
                                                            film.metacritic_rating
                                                        }}
                                                    </p>
                                                </h3>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-3">
                                        <h2 class="text-xl font-semibold">
                                            Genres
                                        </h2>
                                        <div class="flex gap-2 flex-wrap">
                                            <Link
                                                v-for="genre in film.genres"
                                                :href="`/film/genres/${genre.slug}`"
                                                prefetch="click"
                                                class="rounded-full px-3 py-2 bg-orange-600 hover:bg-orange-600 text-white text-sm"
                                                cache-for="30s"
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

                <!-- awards section -->
                <div v-if="film.festival_awards">
                    <h3

                        class="text-[#141414] text-lg font-bold leading-tight tracking-[-0.015em] px-4 pb-2 pt-4"
                    >
                        Awards Details
                    </h3>
                    <div class="py-5 px-4">
                        <p
                            class="flex gap-3 items-center border-b py-3 text-[#141414] text-md font-normal leading-normal"
                            v-for="award in film.festival_awards.split('|')"
                        >
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
                                class="lucide lucide-trophy-icon lucide-trophy"
                            >
                                <path
                                    d="M10 14.66v1.626a2 2 0 0 1-.976 1.696A5 5 0 0 0 7 21.978"
                                />
                                <path
                                    d="M14 14.66v1.626a2 2 0 0 0 .976 1.696A5 5 0 0 1 17 21.978"
                                />
                                <path d="M18 9h1.5a1 1 0 0 0 0-5H18" />
                                <path d="M4 22h16" />
                                <path
                                    d="M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z"
                                />
                                <path d="M6 9H4.5a1 1 0 0 1 0-5H6" />
                            </svg>
                            {{ award }}
                        </p>
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
</script>
