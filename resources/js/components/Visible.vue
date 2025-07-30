<!-- resources/js/components/Visible.vue -->
<template>
  <WhenVisible v-bind="$attrs">
    <template v-if="loading">
      <div class="text-center py-3">Loading...</div>
    </template>
  </WhenVisible>
</template>

<script setup>
import { WhenVisible } from '@inertiajs/vue3';

defineProps({
  loading: Boolean,
});
</script>
