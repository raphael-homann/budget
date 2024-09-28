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
import BudgetPageHeader from "../component/budget-page-header.vue";

const BudgetComponent = defineComponent({
  components: {BudgetPageHeader, BgCategoryEditForm, BgEnvelopeEditForm, ImportModal},
  mixins: [
      routeMixin
  ],
  data(): {
    budgetRepository: Repository<Budget>
    budget: null | Budget
    budgetId: null | number,
  } {
    return {
      budgetRepository: store.entityManager.getRepository(Budget),
      budget: null,
      budgetId: null,
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
    this.initBudget();
  }
});

export default BudgetComponent;
</script>

<template >
  <budget-page-header :budget="budget"></budget-page-header>
</template>

<style scoped>

</style>
