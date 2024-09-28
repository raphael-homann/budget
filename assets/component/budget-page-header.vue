<script lang="ts">
import Budget from "../Data/Entity/Budget";
import ImportModal from "./import-modal.vue";


export default {
  name: "budget-page-header",
  components: {ImportModal},
  props: {
    budget: {
      type: Budget,
      required: true
    }
  },
  data() {
    return {
      showImportModal: false
    }
  }
}
</script>

<template>
  <template v-if="budget">
    <v-dialog v-model="showImportModal" max-width="500" @close="showImportModal=false">
      <import-modal :budget-id="budget.id" @finished="showImportModal=false"></import-modal>
    </v-dialog>
    <v-container fluid>
      <h1>Budget : {{ budget.name }}</h1>
      <v-btn :to="{name:'budget-envelopes',params:{budgetId:budget.id}}">Envelopes</v-btn>
      <v-btn :to="{name:'budget-categories',params:{budgetId:budget.id}}">Catégories</v-btn>
      <v-btn :to="{name:'budget-movements',params:{budgetId:budget.id}}">Mouvements</v-btn>
      <v-btn :to="{name:'budget-movements-v2',params:{budgetId:budget.id}}">Mouvements V2</v-btn>
      <v-btn @click="showImportModal=true">Importer</v-btn>
    </v-container>
  </template>
</template>

<style scoped>

</style>
