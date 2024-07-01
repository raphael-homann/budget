

<template id="Home">
  <h1>
    <v-icon>mdi-home</v-icon>
    {{ t('budget.menu.home') }}
  </h1>
  <v-dialog v-model="budgetModal" max-width="500" >
    <bg-budget-edit-form :entity-manager="entityManager" :budget="budgetModal" @close="budgetModal=null" @save="budgetModal=null"></bg-budget-edit-form>
  </v-dialog>

  <v-btn @click="createBudget" color="primary">Ajouter</v-btn>
  <v-row>
<!--    <v-col cols="4">-->

  <budget-card class="col-md-4" v-for="budget in budgets" :budget="budget" @delete="deleteBudget" @edit="editBudget"></budget-card>
<!--    </v-col>-->
  </v-row>
</template>

<script lang="ts">
import {defineComponent} from "vue";

import {store} from "../service/store";
import Budget from "../Data/Entity/Budget";
import BudgetCard from "../component/budget-card.vue";
import BgBudgetEditForm from "../Data/Form/BgBudgetEditForm.vue";
import EntityManager from "@efrogg/synergy/Data/EntityManager";
import ListChangedEvent from "@efrogg/synergy/Data/Event/ListChangedEvent";
import Repository from "@efrogg/synergy/Data/Repository";
import { useLocale } from "vuetify/lib/framework.mjs";


const Home = defineComponent({
  components: {BgBudgetEditForm, BudgetCard},
  setup() {
    const { t } = useLocale()
    return {
      t
    }
  },
  data(): {
    budgets: Budget[],
    budgetModal: null | Budget,
    entityManager: EntityManager,
    budgetRepository:Repository<Budget>
  } {
    return {
      budgets: [],
      budgetModal: null,
      entityManager: store.entityManager,
      budgetRepository: store.entityManager.getRepository(Budget)
    }
  },computed: {
  },
  methods: {
    initBudgets() {
      console.log(this.$router.getRoutes());
      console.log('initBudgets')
      this.budgets = store.entityManager.getRepository(Budget).getItems();
    },
    deleteBudget(budget: Budget) {
      this.entityManager.delete(budget);
      // this.initBudgets();
    },
    editBudget(budget: Budget) {
      this.budgetModal = budget.clone();
      // this.$router.push({name: 'budget-edit', params: {id: budget.getId()}});
    },
    createBudget() {
      this.budgetModal = new Budget();
    },
    unbindRepository() {
      this.budgetRepository.removeEventListener(ListChangedEvent.TYPE, this.initBudgets);
    },
    bindRepository() {
      this.budgetRepository.addEventListener(ListChangedEvent.TYPE, this.initBudgets);
    },
  },
  unmounted() {
    this.unbindRepository();
    this.bindRepository();
  },
  mounted() {
    this.bindRepository()
    this.initBudgets();
  },
});
export default Home;
</script>
