import { computed,ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { useFilmSearchSort } from "./useFilmSearchSort"; // ✅ Correct

export function useFilmFilters(includeGenre = false) {
    const page = usePage();
    const { search, sort_by } = useFilmSearchSort(); // ✅ Correct

    const films = computed(() => page.props.films?.data || []);
    const reachedEnd = computed(
        () =>
            page.props.pagination?.current_page >=
            page.props.pagination?.last_page
    );

    const allGenres = computed(() => page.props.genres || []);
    const selectedGenre = computed(() => page.props.genre || null);
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
        onSuccess: () => (loading.value = false),
        onFinish: () => (loading.value = false),
    }));

    function sortBy(value) {
        sort_by.value = value;
    }

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
