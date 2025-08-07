<template>
    <article
        class="relative transition-colors duration-300 border-white hover:border-orange-500 border border-opacity-0 rounded-xl"
    >
        <div class="">
            <div
                class="w-full aspect-[3/4] overflow-hidden rounded-xl bg-orange-500"
            >
                <img
                    :src="film.poster"
                    :alt="film.title"
                    class="w-full h-full object-cover"
                />
            </div>
        </div>
        <!-- average rating -->
        <div class="absolute bottom-3 right-2">
            <h3
                class="p-1 text-sm lg:p-0 rounded-full w-8 h-8 md:w-10 md:h-10 flex justify-center items-center"
                :class="{
                    'bg-red-500 text-white': film.average_rating <= 5,
                    'bg-teal-600 text-white':
                        film.average_rating > 5 && film.average_rating <= 7.5,
                    'bg-purple-500 text-white': film.average_rating > 7.5,
                }"
            >
                {{ film.average_rating.toFixed(1) }}
            </h3>
        </div>
    </article>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    film: Object,
});
</script>

<!-- <style scoped>
:root {
  --card-radius: 20rem;
  --bg: #ffffff;
  --text-primary: #1f2d3a;
  --text-secondary: #555f7a;
  --badge-bg: #2563eb;
  --badge-color: #fff;
  --transition: .25s cubic-bezier(.4,.2,.2,1);
  --hover-ring: rgba(186, 189, 194, 0.35);
}

.movie-card {
  /* background: var(--bg); */
  border-radius: 20px;
  border: 1px solid;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  width: 100%;
  font-family: system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
  color: var(--text-primary);
  box-shadow: none; /* removed shadow */
  transition: transform var(--transition), box-shadow var(--transition), filter var(--transition);
  position: relative;
  will-change: transform;
}

.movie-card:hover {
  transform: scale(1.02);
  /* subtle glow ring */
  box-shadow: 0 0 0 2px white;
  filter: brightness(1.02);
}

/* Poster aspect ratio 2:3, contains whole image */
.poster-wrapper {
  position: relative;
  width: 100%;
  aspect-ratio: 2 / 3;
  background: #000; /* fallback/letterboxing */
  overflow: hidden;
}

.poster-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover; /* fills, cropping if necessary */
  display: block;
}


/* Optional badge in top-left */
.badge {
  position: absolute;
  top: 8px;
  left: 8px;
  width: 40px;
  height: 40px;
  background-color: rgb(243, 84, 16);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.65rem;
  font-weight: 600;
  border-radius: 50%;
  line-height: 1;
  box-sizing: border-box;
}


/* Info row: title and year */
.info {
  padding: 0.75rem 1rem 1rem;
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.title {
  font-weight: 600;
  flex: 1 1 auto;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin-right: 8px;
}

.year {
  flex: 0 0 auto;
  color: var(--text-secondary);
  font-size: 0.75rem;
}
</style> -->
