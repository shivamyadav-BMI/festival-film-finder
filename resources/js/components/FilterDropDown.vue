<template>
   <div ref="dropdownRef" class="relative inline-block">
        <button
            @click.stop="toggleFilterDropdown"
            id="dropdownDefaultButton"
            class="text-white bg-orange-500 hover:bg-orange-600 font-medium rounded-lg text-sm px-2 py-1 md:px-5 md:py-2.5 text-center inline-flex items-center cursor-pointer"
            type="button"
        >
            Filter By
            <svg
                class="w-2.5 h-2.5 ms-3"
                aria-hidden="true"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 10 6"
            >
                <path
                    stroke="currentColor"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="m1 1 4 4 4-4"
                />
            </svg>
        </button>

        <div>
            <div
                v-if="isFilterOpen"
                id="dropdown"
                class="absolute mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-64 border border-gray-100 left-auto right-0"
            >
                <ul class="py-2 text-sm text-gray-700 grid grid-cols-2">
                    <div class="p-3">
                        <h3 class="mb-2 font-semibold text-gray-900">Festivals</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="accent-orange-600" @change="filterByFestival('Oscar')" />
                                <h3>Oscar</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="accent-orange-600" @change="filterByFestival('Cannes')" />
                                <h3>Cannes</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="accent-orange-600" @change="filterByFestival('Venice')" />
                                <h3>Venice</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="checkbox" class="accent-orange-600" @change="filterByFestival('Berlin')" />
                                <h3>Berlin</h3>
                            </div>
                        </div>
                    </div>

                    <div class="p-3">
                        <h3 class="mb-2 font-semibold text-gray-900">Sort by Rating</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sort" class="accent-orange-600" @change="sortBy('rating_asc')" />
                                <h3>Low to High</h3>
                            </div>
                            <div class="flex items-center gap-3">
                                <input type="radio" name="sort" class="accent-orange-600" @change="sortBy('rating_desc')" />
                                <h3>High to Low</h3>
                            </div>
                        </div>

                        <h3 class="mt-4 mb-2 font-semibold text-gray-900">Filter by Year</h3>
                        <div class="space-y-2 max-h-40 overflow-auto">
                            <div v-for="y in [2025,2024,2023,2022,2021,2020]" :key="y" class="flex items-center gap-3">
                                <input type="radio" name="year" class="accent-orange-600" @change="filterByYear(y)" />
                                <h3>{{ y }}</h3>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onBeforeUnmount, onMounted, ref } from "vue";
import { useFilmFilters } from "../composables/useFilmFilters";

const { sortBy } = useFilmFilters(false);

const isFilterOpen = ref(false);
const dropdownRef = ref(null);

function toggleFilterDropdown() {
    isFilterOpen.value = !isFilterOpen.value;
}

function handleClickOutside(event) {
    if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
        isFilterOpen.value = false;
    }
}

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("click", handleClickOutside);
});
</script>
