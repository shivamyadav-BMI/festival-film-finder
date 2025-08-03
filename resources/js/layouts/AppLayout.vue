<template>
    <div class="flex flex-col min-h-screen">
        <!-- NAVIGATION BAR -->
        <nav
            class="relative bg-black sticky w-full z-30 -top-1 start-0 border-b border-gray-200"
        >
            <div
                class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4"
            >
                <Link
                    href="/"
                    class="flex items-center space-x-3 rtl:space-x-reverse"
                >
                    <Link
                        href="/"
                        class="text-orange-600 self-center text-xs md:text-sm lg:text-2xl uppercase font-semibold whitespace-nowrap dark:text-white"
                    >
                        Festival Film Finder
                    </Link>
                </Link>

                <!-- RIGHT SIDE BUTTONS -->
                <div
                    class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse"
                >
                    <!-- Search (Desktop input + Mobile icon) -->
                    <div>
                        <input
                            v-model="search"
                            ref="searchInputRef"
                            type="search"
                            class="hidden lg:block text-black border-white border font-medium rounded-lg text-sm px-4 py-2"
                        />

                        <!-- Mobile Search Icon -->
                        <div
                            class="sm:hidden cursor-pointer"
                            @click="openSearch = true"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-search-icon lucide-search"
                            >
                                <path d="m21 21-4.34-4.34" />
                                <circle cx="11" cy="11" r="8" />
                            </svg>
                        </div>
                    </div>

                    <!-- Mobile Menu Toggle -->
                    <button
                        @click="openMobileSidebar"
                        type="button"
                        class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600"
                        aria-controls="navbar-sticky"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>
                        <svg
                            class="w-5 h-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 17 14"
                        >
                            <path
                                stroke="currentColor"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M1 1h15M1 7h15M1 13h15"
                            />
                        </svg>
                    </button>
                </div>

                <!-- NAV LINKS -->
                <div
                    class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1"
                    id="navbar-sticky"
                >
                    <ul
                        class="flex flex-col items-center p-4 md:p-0 mt-4 font-medium border rounded-lg md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0"
                    >
                        <li>
                            <Link
                                href="/"
                                class="block py-2 px-3 text-lg rounded-sm md:p-0"
                                :class="
                                    urlIs('/')
                                        ? 'text-orange-500'
                                        : 'text-white hover:text-orange-600'
                                "
                                aria-current="page"
                                >Home</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/about"
                                class="block py-2 text-lg px-3 rounded-sm md:p-0"
                                :class="
                                    urlIs('about')
                                        ? 'text-orange-500'
                                        : 'text-white hover:text-orange-600'
                                "
                                >About</Link
                            >
                        </li>
                        <li>
                            <div class="w-full space-y-2">
                                <button
                                    @click="toggleGenreDropdown"
                                    class="relative flex justify-between gap-2 border p-2 w-full text-start rounded"
                                >
                                    <span>All Genres</span>
                                    <!-- open icon -->
                                    <div v-if="!isGenreDropdownOpen">
                                        <ChevronDown />
                                    </div>
                                    <div v-if="isGenreDropdownOpen">
                                        <ChevronUp />
                                    </div>
                                </button>
                                <!-- all genres dropdown  -->
                                <div
                                    v-if="isGenreDropdownOpen"
                                    class="absolute border rounded-md h-72 z-32 shadow-xl bg-black overflow-y-auto"
                                >
                                    <Link
                                        class="hover:bg-orange-600 block p-2 mb-1"
                                        :class="{
                                            'bg-orange-500':
                                                usePage().props?.genre ==
                                                genre.slug,
                                        }"
                                        :href="`/film/genres/${genre.slug}`"
                                        v-for="genre in allGenres"
                                        >{{ genre.name }}</Link
                                    >
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- MOBILE SEARCH OVERLAY -->
            <transition name="fade-slide">
                <div
                    v-show="openSearch"
                    @click.self="openSearch = false"
                    class="absolute top-0 left-0 bg-orange-600 z-50 w-full h-full text-white transition-all duration-300 ease-in-out transform"
                    :class="
                        openSearch
                            ? 'opacity-100 translate-y-0'
                            : 'opacity-0 -translate-y-10'
                    "
                >
                    <div
                        class="flex px-3 justify-between gap-5 items-center h-full max-w-screen-lg mx-auto"
                    >
                        <input
                            type="search"
                            v-model="search"
                           ref="mobileSearchRef"
                            class="border border-white bg-white text-black w-full rounded-xl p-2"
                            placeholder="Search..."
                        />
                        <!-- Close Icon -->
                        <div
                            @click="openSearch = false"
                            class="text-white font-semibold text-xl cursor-pointer"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                width="24"
                                height="24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-x-icon lucide-x"
                            >
                                <path d="M18 6 6 18" />
                                <path d="m6 6 12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- MOBILE NAVBAR -->
        <aside
            v-if="isOpen"
            :class="[
                'fixed top-0 right-0 h-full w-3/4 max-w-sm  z-50 shadow-lg transform transition-transform ease-in-out duration-300',
                isOpen ? 'translate-x-0' : 'translate-x-full',
            ]"
            class="bg-black w-64 border-r fixed h-screen z-50 shadow"
        >
            <div class="py-5 px-4">
                <div class="mb-10 text-md text-orange-500 uppercase">
                    <span
                        class=""
                        title="close sidebar"
                        @click="closeMobileSidebar"
                    >
                        <PanelRightClose />
                    </span>
                </div>
                <div class="flex flex-col gap-5">
                    <Link href="/" class="hover:text-orange-600">Home</Link>
                    <Link href="/about" class="hover:text-orange-600"
                        >About</Link
                    >

                    <div class="w-full space-y-2">
                        <button
                            @click="toggleGenreDropdown"
                            class="flex justify-between gap-2 border p-2 w-full text-start rounded"
                        >
                            <span>All Genres</span>
                            <!-- open icon -->
                            <div v-if="!isGenreDropdownOpen">
                                <ChevronDown />
                            </div>
                            <div v-if="isGenreDropdownOpen">
                                <ChevronUp />
                            </div>
                        </button>
                        <!-- all genres dropdown  -->
                        <div
                            v-if="isGenreDropdownOpen"
                            class="bg- border rounded-md h-72 z-32 shadow overflow-y-auto"
                        >
                            <Link
                                class="hover:bg-orange-600 block p-2 mb-1"
                                :class="{
                                    'bg-orange-500':
                                        usePage().props?.genre == genre.slug,
                                }"
                                :href="`/film/genres/${genre.slug}`"
                                v-for="genre in allGenres"
                                >{{ genre.name }}</Link
                            >
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 px-5 md:px-0 mb-20">
            <slot></slot>
        </div>

        <!-- FOOTER -->
        <footer
            class="bg-black border-t mt-auto border-opacity-5 px-2 text-center md:px-10"
        >
            <div class="my-5">
                &copy; 2025
                <Link href="/" class="text-orange-500 hover:text-orange-600"
                    >Festival Film Finder</Link
                >. All rights reserved.
            </div>
        </footer>
    </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { useFilmFilters } from "../composables/useFilmFilters";
import { computed } from "vue";
import { ChevronDown, ChevronUp, PanelRightClose } from "lucide-vue-next";
import { nextTick } from "vue";

const page = usePage();
// const emit = defineEmits(["update:search", "genreSelected"]);
const searchInputRef = ref(null);
const mobileSearchRef = ref(null);

const { search, allGenres, filterByGenre } = useFilmFilters(
    false,
    searchInputRef
);
const selectedGenre = ref(page.props.genre || null);
const openSearch = ref(false);

// active url
const currentUrl = computed(() => page.props.current_url);
function urlIs(url) {
    return currentUrl.value == url;
}

watch(selectedGenre, () => {
    if (selectedGenre.value == null) {
        router.get("/");
    } else {
        router.get(`/film/genres/${selectedGenre.value}`);
    }
});

// close and open the mobile navbar
const isOpen = ref(false);

function openMobileSidebar() {
    isOpen.value = true;
}

function closeMobileSidebar() {
    isOpen.value = false;
}

// all genres dropdown open and close
const isGenreDropdownOpen = ref(false);
function toggleGenreDropdown() {
    isGenreDropdownOpen.value = !isGenreDropdownOpen.value;
}

// const searchInputRef = ref(null);
watch(openSearch, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            mobileSearchRef.value?.focus();
        });
    }
});

onMounted(() => {
    if (search.value?.trim() == null) return;
    if (page.props.current_url == "/") {
        nextTick(() => {
            setTimeout(() => {
                searchInputRef.value?.focus();
            }, 100);
        });
    }
});
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: opacity 0.3s, transform 0.3s;
}

.fade-slide-enter,
.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}
</style>
