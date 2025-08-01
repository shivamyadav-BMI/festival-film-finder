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

        <div
            v-if="isFilterOpen"
            id="dropdown"
            class="absolute mt-2 z-10 bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-28 border border-gray-100"
        >
            <ul class="py-2 text-sm text-gray-700 w-full dark:text-gray-200">
                <li>
                    <button
                        @click="sortBy('asc')"
                        class="text-start block px-4 py-2 hover:bg-gray-100 w-full cursor-pointer hover:bg-orange-500 hover:text-white"
                    >
                        Low to high
                    </button>
                </li>
                <li>
                    <button
                        @click="sortBy('desc')"
                        class="text-start block px-4 py-2 hover:bg-gray-100 w-full cursor-pointer hover:bg-orange-500 hover:text-white"
                    >
                        High to low
                    </button>
                </li>
            </ul>
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
