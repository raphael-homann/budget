<script lang="ts">
import {defineComponent} from "vue";
import BgEnvelopeEditForm from "../Data/Form/BgEnvelopeEditForm.vue";
import ImportModal from "../component/import-modal.vue";
import Budget from "../Data/Entity/Budget";
import {store} from "../service/store";
import Repository from "@efrogg/synergy/Data/Repository";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
import routeMixin from "../mixin/routeParamsMixin";
import BgCategoryEditForm from "../Data/Form/BgCategoryEditForm.vue";

const BudgetComponent = defineComponent({
  components: {BgCategoryEditForm, BgEnvelopeEditForm, ImportModal},
  mixins: [
      routeMixin
  ],
  data(): {
    budgetRepository: Repository<Budget>
    budget: null | Budget
    budgetId: null | number,
    showImportModal: boolean
  } {
    return {
      budgetRepository: store.entityManager.getRepository(Budget),
      budget: null,
      budgetId: null,
      showImportModal: false
    }
  },
  methods: {
    initBudget() {
      if(this.budgetId) {
        this.budget = this.budgetRepository.get(this.budgetId);
        console.log('this.budget', this.budget)
      }
    }
  },
  mounted() {
    this.budgetRepository.addEventListener(ListChangedEvent.TYPE, this.initBudget);
    this.budgetId = this.getRouteParamInt(this.$route,'id');
    console.log('budgetId', this.budgetId)
    this.initBudget();
  }
});

export default BudgetComponent;
</script>

<template>

  <v-dialog v-model="showImportModal" max-width="500" @close="showImportModal=false">
    x
    <import-modal :budget-id="budgetId" @finished="showImportModal=false"></import-modal>
    x
  </v-dialog>



  <v-container fluid v-if="budget">
    <h1>Budget : {{ budget.name }}</h1>
    <v-btn :to="{name:'budget-envelopes',params:{budgetId:budget.id}}">Envelopes</v-btn>
    <v-btn :to="{name:'budget-categories',params:{budgetId:budget.id}}">Catégories</v-btn>
    <v-btn :to="{name:'budget-movements',params:{budgetId:budget.id}}">Mouvements</v-btn>
    <v-btn @click="showImportModal=true">Importer</v-btn>
  </v-container>
</template>

<style scoped>

</style>
