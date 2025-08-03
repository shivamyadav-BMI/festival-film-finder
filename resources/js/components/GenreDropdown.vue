<template>
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
                    'bg-orange-500': usePage().props?.genre == genre.slug,
                }"
                :href="`/film/genres/${genre.slug}`"
                v-for="genre in allGenres"
                >{{ genre.name }}</Link
            >
        </div>
    </div>
</template>

<script setup>
// onMounted(() => {
//     if (search.value?.trim() == null) return;
//     if (page.props.current_url == "/") {
//         nextTick(() => {
//             setTimeout(() => {
//                 searchInputRef.value?.focus();
//             }, 100);
//         });
//     }
// });
</script>


