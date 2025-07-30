<template>
    <div class="dropdown">
        <a
            class="btn btn-secondary dropdown-toggle"
            href="#"
            role="button"
            id="dropdownMenuLink"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >
            Filter by
        </a>
        <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <li role="button" @click="sortBy('asc')" class="dropdown-item">
                Low to high (rating)
            </li>
            <li role="button" @click="sortBy('desc')" class="dropdown-item">
                High to low (rating)
            </li>
        </div>
    </div>
</template>

<script setup>
import { useFilmFilters } from '../composables/useFilmFilters';

const {sortBy} = useFilmFilters(false);
</script>
