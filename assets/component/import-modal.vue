<script lang="ts">


export default {
  name: "import-modal",
  props: {
    budgetId: {
      type: Number,
      required: true
    }
  },
  data(): {
    file: File | null,
    dryRun: boolean,
    overwrite: boolean,
    errorMessage: string|null,
    stats: { string: string }|null
  } {
    return {
      file: null,
      dryRun: true,
      overwrite: false,
      errorMessage: null,
      stats: null
    }
  },
  methods: {
    importFile() {

      this.errorMessage = null;
      this.stats = null;

      if (!this.file) {
        this.setError('No file selected');
        return;
      }

      let formData = new FormData();
      formData.append('budget-id', this.budgetId?.toString());
      formData.append('file', this.file)
      formData.append('dry-run', this.dryRun ? '1' : '0');
      formData.append('overwrite', this.overwrite ? '1' : '0');


      fetch('/budget/import', {
        method: 'POST',
        body: formData
      }).then((response: Response) => {
        response.json().then((json) => {
          if (response.status !== 200) {
            this.setError(json.message);
            console.error('import failed', response)
            return;
          }
          if (json.status !== 'ok') {
            this.setError(json.message);
          }
          this.displayStats(json.statistics)
          // ok, started !!
          console.log('started', response)
        }).catch((error) => {
          console.error('import failed', error)
        });
        // this.loadWorks();
        // this.$emit('finished')
      }).catch((error) => {
        console.log(error)
      });

    },
    setError(message: string): void {
      this.errorMessage = message;
    },
    displayStats(stats: { string: string }) {
      this.stats = stats;
      console.log('stats', stats)
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
            accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel, text/csv"
        ></v-file-input>
      <v-card-actions>
        <v-checkbox v-model="dryRun" label="Dry run"></v-checkbox>
        <v-checkbox v-model="overwrite" label="Overwrite"></v-checkbox>
        <v-form>
          <v-btn @click="$emit('finished')" color="secondary" variant="plain">Close</v-btn>
          <v-btn @click="importFile" color="primary" variant="flat">Import</v-btn>
        </v-form>
      </v-card-actions>
      <VCardText>
        <v-alert v-if="errorMessage" type="error">{{errorMessage}}</v-alert>
        <v-alert v-if="stats" type="info">{{stats}}</v-alert>
      </VCardText>

    </v-card>
  </v-container>
</template>

<style scoped>

</style>
