import { ref, computed, watch, nextTick } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { debounce, throttle } from "lodash";

export function useFilmFilters(includeGenre = false, searchInputRef = null) {
    const page = usePage();
    // const searchInputRef = ref(null);

    const currentUrl = computed(() => page.props.current_url);
    const films = computed(() => page.props.films?.data || []);
    const reachedEnd = computed(
        () =>
            page.props.pagination?.current_page >=
            page.props.pagination?.last_page
    );

    const allGenres = ref(page.props.genres || []);
    const search = ref(page.props.search || null);
    const sort_by = ref(page.props.sort_by || null);
    const selectedGenre = ref(page.props.genre || null);
    const loading = ref(false);

    const whenVisibleParams = computed(() => ({
        data: {
            page: page.props.pagination?.current_page + 1,
            ...(search.value ? { search: search.value } : {}),
            ...(sort_by.value ? { sort_by: sort_by.value } : {}),
        },
        preserveUrl: true,
        preserveState: true,
        preserveScroll: true,
        replace: false,
        only: ["films", "pagination"],
        onBefore: () => (loading.value = true),
        onSuccess: () => {
            loading.value = false;
        },
        onFinish: () => (loading.value = false),
    }));

    function sortBy(value) {
        sort_by.value = value;
    }

    //   watch(
    //         () => currentUrl.value, // Watch the value of currentUrl
    //         throttle((value) => {
    //             const data = {
    //                 search: value,
    //             };

    //             // Only trigger reload if the current path isn't '/'
    //             if (currentUrl.value !== "/") {
    //                 router.get("/", {
    //                     // Pass search parameter directly in query
    //                     query: { search: data.search },
    //                     preserveState: true,
    //                     replace: true,
    //                 });
    //             }
    //         }, 300)
    //     );
    // Watch search and sort changes
    watch(
        search,
        debounce((value) => {
            const data = {
                search: value,
            };

            // testing
            if (
                (currentUrl.value.startsWith("film/") &&
                    !currentUrl.value.startsWith("film/genres/")) ||
                currentUrl.value == "about"
            ) {
                router.visit("/", {
                    data,
                    preserveState: true,
                    replace: true,

                });
            } else {
                router.reload({
                    data,
                    preserveState: true,
                    replace: true,
                    onFinish: () => {
                        setTimeout(() => {
                            searchInputRef.value?.focus();
                        }, 100);
                    },
                });
            }
        }, 300)
    );

    // Watcher for sort_by
    watch(
        sort_by,
        debounce((value) => {
            const data = {};
            if (search.value && search.value.trim() !== "") {
                data.search = search.value;
            }
            if (value && value.trim() !== "") {
                data.sort_by = value;
            }
            router.reload({
                data,
                preserveState: true,
                replace: true,
            });
        }, 300)
    );
    return {
        films,
        searchInputRef,
        reachedEnd,
        allGenres,
        search,
        sort_by,
        selectedGenre,
        whenVisibleParams,
        loading,
        sortBy,
    };
}
