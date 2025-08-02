// composables/useFilmSearchSort.js
import { ref, watch } from "vue";
import { usePage, router } from "@inertiajs/vue3";
import { debounce } from "lodash";

const search = ref("");
const sort_by = ref("");

let initialized = false;

export function useFilmSearchSort() {
    const page = usePage();

    if (!initialized) {
        search.value = page.props.search || "";
        sort_by.value = page.props.sort_by || "";
        initialized = true;
    }

    watch(
        search,
        debounce((value) => {
            const data = { search: value };
            router.reload({
                data,
                preserveState: true,
                replace: true,
            });
        }, 300)
    );

    watch(
        sort_by,
        debounce((value) => {
            const data = {};
            if (search.value?.trim()) data.search = search.value;
            if (value?.trim()) data.sort_by = value;

            router.reload({
                data,
                preserveState: true,
                replace: true,
            });
        }, 300)
    );

    return { search, sort_by };
}
