<template>
 <AppLayout>
        <div class="container mb-5">
            <div class="d-flex justify-end gap-5">
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
                    <div
                        class="dropdown-menu"
                        aria-labelledby="dropdownMenuLink"
                    >
                        <li
                            role="button"
                            @click="sortBy('asc')"
                            class="dropdown-item"
                        >
                            Low to high (rating)
                        </li>
                        <li
                            role="button"
                            @click="sortBy('desc')"
                            class="dropdown-item"
                        >
                            High to low (rating)
                        </li>
                    </div>
                </div>

                <div>
                    <input
                        class="form-control me-2"
                        type="search"
                        placeholder="Search by title or director"
                        v-model="search"
                        aria-label="Search"
                    />
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row g-4">
                <Link
                    :href="`/film/title/${film.title}`"
                    class="col-12 col-sm-6 col-md-4"
                    v-for="film in films"
                    :key="film.id"
                    prefetch="click"
                    cache-for="30s"
                >
                    <div class="card h-100" style="width: 100%">
                        <img
                            :src="film.poster"
                            class="card-img-top"
                            alt="..."
                        />
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ film.title }}</h5>
                            <h5 class="card-title">{{ film.imdb_rating }}</h5>
                        </div>
                    </div>
                </Link>
            </div>
        </div>

        <WhenVisible :always="!reachedEnd" :params="whenVisibleParams">
            <template v-if="loading">
                <div>Loading...</div>
            </template>
        </WhenVisible>
    </AppLayout>
</template>

<script setup>
import { Link, router, usePage, WhenVisible } from "@inertiajs/vue3";
import AppLayout from "../../layouts/AppLayout.vue";
import { computed, ref, watch } from "vue";
import { throttle } from "lodash";

// Composable for WhenVisible loading + params logic
function useWhenVisibleParams(search, sort_by) {
    const page = usePage();
    const loading = ref(false);

    const whenVisibleParams = computed(() => ({
        data: {
            page: page.props.pagination.current_page + 1,
            ...(search.value ? { search: search.value } : {}),
            ...(sort_by.value ? { sort_by: sort_by.value } : {}),
        },
        preserveUrl: true,
        preserveState: true,
        preserveScroll: true,
        replace: false,
        only: ["films", "pagination"],
        onBefore: () => (loading.value = true),
        onSuccess: () => (loading.value = false),
        onFinish: () => (loading.value = false),
    }));

    return { whenVisibleParams, loading };
}

// Setup
const page = usePage();
const films = computed(() => page.props.films);
const reachedEnd = computed(
    () => page.props.pagination.current_page >= page.props.pagination.last_page
);

const search = ref(page.props.search || "");
const sort_by = ref(page.props.sort_by || null);

// Use the composable
const { whenVisibleParams, loading } = useWhenVisibleParams(search, sort_by);

// Sorting logic
function sortBy(value) {
    sort_by.value = value;
}

// Watch for search/sort changes and trigger page reload
watch(
    [search, sort_by],
    throttle(([searchValue, sortByValue]) => {
        let data = {

        };
        data.search = searchValue?.trim() || "";
        if (sortByValue) data.sort_by = sortByValue;

        router.reload({
            data,
            preserveState: true,
            replace: true,
        });
    }, 2000)
);
</script>
