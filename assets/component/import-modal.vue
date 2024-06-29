<script lang="ts">


export default {
  name: "import-modal",
  props: {
    budgetId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      file: null
    }
  },
  methods: {
    importFile() {

      console.log('importFile', this.file)
      this.$emit('finished')
    }
  },
  emits: ['finished']

}
</script>

<template>
  <v-container fluid>
    <v-card title="Importer">
        <v-file-input
            v-model="file"
            label="File input"
            hint="Select a file"
            persistent-hint
            prepend-icon="mdi-paperclip"
            accept="application/json"
        ></v-file-input>
        <v-btn @click="importFile">Import</v-btn>

    </v-card>
  </v-container>
</template>

<style scoped>

</style>
