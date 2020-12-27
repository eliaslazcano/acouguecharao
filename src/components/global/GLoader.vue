<template>
  <v-overlay :value="$store.state.loader">
    <v-progress-circular indeterminate size="48"></v-progress-circular>
  </v-overlay>
</template>

<script>
export default {
  name: 'GLoader'
}
</script>
