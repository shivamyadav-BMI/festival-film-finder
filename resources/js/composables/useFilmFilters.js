import { ref, computed, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { debounce, throttle } from "lodash";

export function useFilmFilters(includeGenre = false) {
    const page = usePage();

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

    // Watch search and sort changes
    watch(
        search,
        throttle((value) => {
            const data = {
                search: value,
            };

            router.reload({
                data,
                preserveState: true,
                replace: true,
            });
        }, 500)
    );

    // Watcher for sort_by
    watch(
        sort_by,
        throttle((value) => {
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
        }, 500)
    );
    return {
        films,
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
