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
                        <div
                            class="relative hidden lg:block"
                            @click="$refs.searchInputRef.focus()"
                        >
                            <!-- Search icon (only visible when input is not focused and empty) -->
                            <div
                                v-if="!searchFocused && !search"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-orange-500 pointer-events-none"
                            >
                                <Search />
                            </div>

                            <input
                                v-model="search"
                                ref="searchInputRef"
                                type="search"
                                @focus="searchFocused = true"
                                 @blur="() => { if (!search) searchFocused = false }"
                                class="text-black outline-none ring-0 focus:border-white focus:border font-medium rounded-lg text-sm px-2 py-2 w-12 cursor-pointer focus:w-64 transition-all duration-300 ease-out origin-left"
                            />
                        </div>

                        <!-- Mobile Search Icon  -->
                        <div
                            class="lg:hidden cursor-pointer"
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
                        class="inline-flex text-orange-600 hover:text-white hover:bg-orange-500 items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden focus:outline-none focus:ring-2 focus:ring-gray-200"
                        aria-controls="navbar-sticky"
                        aria-expanded="false"
                    >
                        <span class="sr-only">Open main menu</span>
                        <!-- open mobile sidebar icon -->
                        <Text />
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
                        <li class="relative w-full">
                            <!-- Overlay for outside click -->
                            <div
                                v-if="isGenreDropdownOpen"
                                @click.self="closeGenreDropdown"
                                class="fixed inset-0 z-10"
                            ></div>

                            <div class="space-y-2 relative z-20 w-full">
                                <button
                                    @click="toggleGenreDropdown"
                                    class="flex justify-between items-center gap-2 border p-2 w-full text-start rounded"
                                >
                                    <span class="truncate">{{
                                            "All Genres"
                                    }}</span>
                                    <div>
                                        <ChevronDown
                                            v-if="!isGenreDropdownOpen"
                                        />
                                        <ChevronUp v-else />
                                    </div>
                                </button>

                                <!-- Dropdown -->
                                <div
                                    v-if="isGenreDropdownOpen"
                                    class="absolute border rounded-md max-h-72 z-30 shadow-xl bg-black overflow-y-auto p-2"
                                >
                                    <Link
                                        v-for="genre in allGenres"
                                        :key="genre.slug"
                                        :href="`/film/genres/${genre.slug}`"
                                        class="block hover:bg-orange-600 px-3 py-2 mb-1 rounded whitespace-nowrap"
                                        :class="{
                                            'bg-orange-500':
                                                usePage().props?.genre ===
                                                genre.slug,
                                        }"
                                    >
                                        {{ genre.name }}
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- MOBILE SEARCH OVERLAY -->
            <transition
                enter-active-class="transition duration-500 ease-out"
                leave-active-class="transition duration-500 ease-in"
                enter-from-class="-translate-y-full opacity-0"
                enter-to-class="translate-y-0 opacity-100"
                leave-from-class="translate-y-0 opacity-100"
                leave-to-class="-translate-y-full opacity-0"
            >
                <div
                    v-show="openSearch"
                    @click.self="openSearch = false"
                    class="fixed top-0 left-0 bg-black z-50 w-full h-full text-white transform"
                >
                    <div
                        class="relative flex px-3 justify-between gap-3 items-center h-full max-w-screen-lg mx-auto"
                    >
                        <!-- Mobile input -->
                        <input
                            type="search"
                            v-model="mobileSearch"
                            ref="mobileSearchRef"
                            class="border border-white bg-white text-black w-full rounded-xl p-2 focus:outline-none focus:ring-0"
                            placeholder="Search..."
                        />

                        <!-- Trigger Search -->
                        <button
                            @click="triggerMobileSearch"
                            class="top-0 px-3 py-2 bg-white text-orange-600 font-semibold rounded-lg hover:bg-orange-100 transition"
                        >
                            <Search />
                        </button>

                        <!-- Close Icon -->
                        <div
                            @click="openSearch = false"
                            class="absolute hover:bg-orange-500 hover: rounded-full p-2 right-5 top-5 font-semibold text-xl text-white font-semibold text-xl cursor-pointer"
                        >
                            <X />
                        </div>
                    </div>
                </div>
            </transition>
        </nav>

        <!-- MOBILE NAVBAR -->
        <transition
            enter-active-class="transition-transform duration-500 ease-out"
            leave-active-class="transition-transform duration-500 ease-in"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-show="isOpen"
                class="fixed inset-0 z-50"
                @click.self="closeMobileSidebar"
            >
                <div
                    class="bg-black w-3/4 max-w-sm h-full absolute right-0 shadow-lg border-l transform transition-transform ease-in-out duration-300"
                >
                    <div class="py-5 px-4">
                        <div
                            class="mb-10 text-md cursor-pointer text-orange-500 uppercase"
                        >
                            <span
                                title="close sidebar"
                                @click="closeMobileSidebar"
                            >
                                <PanelRightClose />
                            </span>
                        </div>
                        <div class="flex flex-col gap-5">
                            <Link
                                href="/"
                                :class="
                                    urlIs('/')
                                        ? 'text-orange-500'
                                        : 'text-white hover:text-orange-600'
                                "
                                class="hover:text-orange-600"
                                >Home</Link
                            >
                            <Link
                                :class="
                                    urlIs('about')
                                        ? 'text-orange-500'
                                        : 'text-white hover:text-orange-600'
                                "
                                href="/about"
                                class="hover:text-orange-600"
                                >About</Link
                            >

                            <div class="w-full space-y-2">
                                <button
                                    @click="toggleGenreDropdown"
                                    class="flex justify-between gap-2 border p-2 w-full text-start rounded"
                                >
                                    <span>All Genres</span>
                                    <div v-if="!isGenreDropdownOpen">
                                        <ChevronDown />
                                    </div>
                                    <div v-if="isGenreDropdownOpen">
                                        <ChevronUp />
                                    </div>
                                </button>
                                <div
                                    v-if="isGenreDropdownOpen"
                                    class="bg- border rounded-md h-72 z-32 shadow overflow-y-auto"
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
                        </div>
                    </div>
                </div>
            </div>
        </transition>

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
import {
    ChevronDown,
    ChevronUp,
    PanelRightClose,
    Search,
    Text,
    X,
} from "lucide-vue-next";
import { nextTick } from "vue";

const page = usePage();
const searchFocused = ref(false);
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

function closeGenreDropdown() {
    isGenreDropdownOpen.value = false;
}

// mobile search trigger function
const mobileSearch = ref(search.value || ""); // local mobile search input
const triggerMobileSearch = () => {
    search.value = mobileSearch.value;
    //close the mobile search
    openSearch.value = false;
};

// const searchInputRef = ref(null);
watch(openSearch, (isOpen) => {
    if (isOpen) {
        nextTick(() => {
            mobileSearchRef.value?.focus();
        });
    }
});

onMounted(() => {
    // if (search.value?.trim() == null) return;
    if (page.props.current_url == "/" && search.value?.trim() != null) {
        nextTick(() => {
            setTimeout(() => {
                searchInputRef.value?.focus();
            }, 100);
        });
    }
});
</script>

