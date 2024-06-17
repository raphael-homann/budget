<script lang="ts">
import {defineComponent} from "vue";
import BgEnvelopeEditForm from "../Data/Form/BgEnvelopeEditForm.vue";
import Budget from "../Data/Entity/Budget";
import {store} from "../service/store";
import Repository from "@efrogg/synergy/Data/Repository";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
const EnvelopeList = defineComponent({
  components: {BgEnvelopeEditForm},
  mixins: [],
  data(): {
    budgetRepository: Repository<Budget>
    budget: null | Budget
    budgetId: null | number
  } {
    return {
      budgetRepository: store.entityManager.getRepository(Budget),
      budget: null,
      budgetId: null
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
    this.budgetId = this.$route.params.id;
    console.log('budgetId', this.budgetId)
    this.initBudget();
  }
});

export default EnvelopeList;
</script>

<template>

  <v-container fluid v-if="budget">
    <h1>Budget : {{ budget.name }}</h1>
    <v-btn :to="{name:'budget-envelopes',params:{budgetId:budget.id}}">Envelopes</v-btn>
  </v-container>
</template>

<style scoped>

</style>
